import nodemailer from 'nodemailer';
import dns from 'dns';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { env } from '../config/env.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const LOG_DIR = path.resolve(__dirname, '../../logs');

// Ensure log directory exists
try {
  if (!fs.existsSync(LOG_DIR)) {
    fs.mkdirSync(LOG_DIR, { recursive: true });
  }
} catch (e) {}

const DEFAULT_RECIPIENTS = ['Care@thedriveclinic.com', 'thedriveclinic@gmail.com'];

/**
 * Returns deduplicated list of configured notification email addresses,
 * always ensuring Care@thedriveclinic.com and thedriveclinic@gmail.com are included.
 */
export function getNotificationRecipients() {
  const configured = (env.NOTIFICATION_EMAIL || '')
    .split(',')
    .map((e) => e.trim())
    .filter(Boolean);
  const combined = [...DEFAULT_RECIPIENTS, ...configured];
  const seen = new Set();
  const unique = [];
  for (const email of combined) {
    const lower = email.toLowerCase();
    if (!seen.has(lower)) {
      seen.add(lower);
      unique.push(email);
    }
  }
  return unique;
}

/**
 * Creates Nodemailer Transporter configured for GoDaddy Business Email / Custom SMTP
 */
function createSmtpTransporter() {
  if (!env.SMTP_PASS) {
    return null;
  }

  const port = Number(env.SMTP_PORT) || 465;
  const isSecure = port === 465 ? true : env.SMTP_SECURE !== false;

  return nodemailer.createTransport({
    host: env.SMTP_HOST || 'smtpout.secureserver.net',
    port,
    secure: isSecure,
    auth: {
      user: env.SMTP_USER || 'Care@thedriveclinic.com',
      pass: env.SMTP_PASS
    },
    tls: {
      rejectUnauthorized: false
    },
    connectionTimeout: 8000,
    greetingTimeout: 8000
  });
}

/**
 * Fallback 1: Direct MX SMTP delivery when SMTP_PASS is not configured
 */
async function sendViaDirectMx({ from, recipients, replyTo, subject, html, text }) {
  const resolveMxWithTimeout = (domain, ms = 2500) =>
    Promise.race([
      dns.promises.resolveMx(domain),
      new Promise((_, reject) => setTimeout(() => reject(new Error('DNS MX lookup timeout')), ms))
    ]);

  return Promise.all(
    recipients.map(async (rcpt) => {
      const domain = rcpt.split('@')[1];
      if (!domain) return { recipient: rcpt, delivered: false, method: 'DIRECT_MX', error: 'Invalid domain' };
      try {
        const mxRecords = await resolveMxWithTimeout(domain, 2500);
        if (!mxRecords || mxRecords.length === 0) {
          return { recipient: rcpt, delivered: false, method: 'DIRECT_MX', error: 'No MX records' };
        }
        mxRecords.sort((a, b) => a.priority - b.priority);
        const mxHost = mxRecords[0].exchange;

        const mxTransporter = nodemailer.createTransport({
          host: mxHost,
          port: 25,
          secure: false,
          name: 'thedriveclinic.com',
          tls: { rejectUnauthorized: false },
          connectionTimeout: 3500,
          greetingTimeout: 3500,
          socketTimeout: 4000
        });

        const info = await mxTransporter.sendMail({
          from,
          to: rcpt,
          replyTo,
          subject,
          text,
          html
        });
        return { recipient: rcpt, delivered: true, method: 'DIRECT_MX', messageId: info.messageId };
      } catch (err) {
        return { recipient: rcpt, delivered: false, method: 'DIRECT_MX', error: err.message };
      }
    })
  );
}

/**
 * Fallback 2: HTTPS FormSubmit AJAX Relay (Port 443, works even when SMTP ports 25/465/587 are blocked or unconfigured)
 */
async function sendViaFormSubmitRelay({ recipients, subject, fields }) {
  const results = [];
  const primary = recipients[0] || 'Care@thedriveclinic.com';
  const ccList = recipients.slice(1).join(',');

  for (const rcpt of recipients) {
    try {
      const controller = new AbortController();
      const timeout = setTimeout(() => controller.abort(), 6000);
      const res = await fetch(`https://formsubmit.co/ajax/${encodeURIComponent(rcpt)}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          Origin: 'https://thedriveclinic.com',
          Referer: 'https://thedriveclinic.com/contact'
        },
        body: JSON.stringify({
          _subject: subject,
          _template: 'table',
          _captcha: 'false',
          ...(rcpt === primary && ccList ? { _cc: ccList } : {}),
          ...fields
        }),
        signal: controller.signal
      });
      clearTimeout(timeout);
      const data = await res.json().catch(() => ({}));
      const ok = res.ok && (data.success === 'true' || data.success === true);
      results.push({ recipient: rcpt, delivered: ok, method: 'HTTPS_RELAY', response: data });
    } catch (err) {
      results.push({ recipient: rcpt, delivered: false, method: 'HTTPS_RELAY', error: err.message });
    }
  }
  return results;
}

/**
 * Unified Multi-Channel Dispatcher
 */
async function dispatchMultiChannelEmail({ type, recipients, replyTo, subject, html, text, relayFields, rawPayload }) {
  const toHeader = recipients.join(', ');
  const fromAddress = env.NOTIFICATION_FROM || '"The Drive Clinic" <Care@thedriveclinic.com>';

  logEmailToAudit(type, toHeader, subject, rawPayload);

  // 1. Try Authenticated SMTP if configured
  const smtpTransporter = createSmtpTransporter();
  if (smtpTransporter) {
    try {
      const info = await smtpTransporter.sendMail({
        from: fromAddress,
        to: toHeader,
        cc: 'thedriveclinic@gmail.com',
        replyTo: replyTo || 'Care@thedriveclinic.com',
        subject,
        text,
        html
      });
      console.log(`[Email Delivered via SMTP]: ${info.messageId} -> ${toHeader}`);
      return { success: true, delivered: true, method: 'SMTP', messageId: info.messageId, recipients };
    } catch (smtpErr) {
      console.warn(`[SMTP Delivery Warning, falling back to Direct MX & HTTPS Relay]:`, smtpErr.message);
    }
  }

  // 2. Try Direct MX & HTTPS Relay in parallel when SMTP password is not set or SMTP failed
  const [mxResults, relayResults] = await Promise.all([
    sendViaDirectMx({ from: fromAddress, recipients, replyTo, subject, html, text }),
    sendViaFormSubmitRelay({ recipients, subject, fields: relayFields })
  ]);

  const anyDelivered =
    mxResults.some((r) => r.delivered) || relayResults.some((r) => r.delivered);

  console.log('----------------------------------------------------');
  console.log(`[Email Notification Dispatched]: ${subject}`);
  console.log(`- Recipients: ${toHeader}`);
  console.log(`- Direct MX:  ${JSON.stringify(mxResults.map((r) => ({ to: r.recipient, ok: r.delivered })))}`);
  console.log(`- HTTPS Relay:${JSON.stringify(relayResults.map((r) => ({ to: r.recipient, ok: r.delivered })))}`);
  console.log('----------------------------------------------------');

  return {
    success: true,
    delivered: anyDelivered,
    recipients,
    mxResults,
    relayResults
  };
}

/**
 * Log email to file audit log for backup
 */
function logEmailToAudit(type, recipient, subject, details) {
  const logFile = path.join(LOG_DIR, 'godaddy_mail_outbox.log');
  const entry = `[${new Date().toISOString()}] TYPE: ${type} | TO: ${recipient} | SUBJECT: ${subject}\nDETAILS: ${JSON.stringify(details, null, 2)}\n------------------------------------------------------------\n`;
  try {
    fs.appendFileSync(logFile, entry);
  } catch (err) {
    console.error('[Mail Audit Log Error]:', err.message);
  }
}

/**
 * Send New Website Lead Email to Care@thedriveclinic.com & thedriveclinic@gmail.com
 */
export async function sendLeadNotificationEmail(lead) {
  const recipients = getNotificationRecipients();
  const cleanPhone = (lead.phone || '').replace(/\D/g, '');
  const waUrl = cleanPhone ? `https://wa.me/${cleanPhone.startsWith('91') ? cleanPhone : '91' + cleanPhone}` : null;
  const leadRef = lead.ref || `LEAD-${lead.lead_id || Date.now().toString().slice(-4)}`;
  const customerName = lead.name || lead.customer_name || 'Website Client';
  const serviceName = lead.service || lead.service_name || lead.service_slug || 'Service Enquiry';
  const vehicleDesc = `${lead.vehicle_type || 'Car'}${lead.vehicle_model ? ` (${lead.vehicle_model})` : ''}${lead.vehicle_reg ? ` - ${lead.vehicle_reg}` : ''}`;

  const subject = `🔥 New Lead Received [${leadRef}]: ${customerName} — ${serviceName}`;

  const text = [
    `THE DRIVE CLINIC // STUDIO MANAGEMENT // JAMMU`,
    `Target Mailboxes: ${recipients.join(', ')}`,
    `====================================================`,
    `EVENT: NEW WEBSITE LEAD`,
    `REFERENCE: ${leadRef}`,
    `CUSTOMER: ${customerName}`,
    `PHONE: ${lead.phone || 'N/A'} ${waUrl ? `(WhatsApp: ${waUrl})` : ''}`,
    `EMAIL: ${lead.email || 'Not provided'}`,
    `VEHICLE: ${vehicleDesc}`,
    `SERVICE: ${serviceName}`,
    `MESSAGE: ${lead.message || lead.notes || 'Direct lead submission from website'}`,
    `SOURCE: ${lead.source || 'Website Contact Form'}`,
    `TIMESTAMP: ${new Date().toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' })} IST`,
    `====================================================`
  ].join('\n');

  const html = `
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { margin:0; padding:0; background:#0b0b0b; font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#f0f0f0; }
    .wrapper { max-width:620px; margin:20px auto; background:#141414; border:1px solid #282828; border-top:4px solid #E50914; border-radius:8px; overflow:hidden; }
    .header { padding:24px 30px; background:#111111; border-bottom:1px solid #222222; }
    .brand-title { font-size:24px; font-weight:900; letter-spacing:1px; color:#ffffff; margin:0; }
    .brand-tag { font-size:11px; font-weight:700; color:#E50914; letter-spacing:2px; text-transform:uppercase; margin-top:4px; }
    .content { padding:30px; }
    .alert-pill { display:inline-block; padding:5px 12px; background:rgba(229,9,20,0.15); border:1px solid #E50914; color:#ff5555; border-radius:20px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:18px; }
    .lead-heading { font-size:20px; font-weight:700; color:#ffffff; margin:0 0 16px 0; }
    .table-details { width:100%; border-collapse:collapse; margin-bottom:24px; font-size:14px; }
    .table-details td { padding:10px 14px; border-bottom:1px solid #222222; }
    .table-details td.label { width:35%; color:#888888; font-weight:600; text-transform:uppercase; font-size:11.5px; letter-spacing:0.5px; }
    .table-details td.value { color:#ffffff; font-weight:500; }
    .value strong { color:#ffffff; }
    .wa-btn { display:inline-block; background:#25D366; color:#ffffff !important; text-decoration:none; padding:6px 14px; border-radius:4px; font-size:12px; font-weight:700; margin-left:8px; }
    .crm-btn { display:block; text-align:center; background:#E50914; color:#ffffff !important; text-decoration:none; padding:14px 24px; border-radius:6px; font-size:14px; font-weight:800; letter-spacing:1px; text-transform:uppercase; margin-top:20px; }
    .footer { padding:20px 30px; background:#0e0e0e; border-top:1px solid #222222; font-size:12px; color:#666666; text-align:center; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <div class="brand-title">THE TDC</div>
      <div class="brand-tag">THE DRIVE CLINIC // STUDIO MANAGEMENT // JAMMU</div>
    </div>
    <div class="content">
      <span class="alert-pill">⚡ NEW WEBSITE ENQUIRY</span>
      <h2 class="lead-heading">A new customer lead just landed from the website:</h2>
      
      <table class="table-details">
        <tr>
          <td class="label">Lead Reference</td>
          <td class="value"><strong style="color:#E50914; font-family:monospace; font-size:15px;">${leadRef}</strong></td>
        </tr>
        <tr>
          <td class="label">Customer Name</td>
          <td class="value"><strong>${customerName}</strong></td>
        </tr>
        <tr>
          <td class="label">Phone / Mobile</td>
          <td class="value">
            <strong>${lead.phone || 'N/A'}</strong>
            ${waUrl ? `<a href="${waUrl}" target="_blank" class="wa-btn">💬 WhatsApp</a>` : ''}
          </td>
        </tr>
        <tr>
          <td class="label">Email Address</td>
          <td class="value">${lead.email || '<em>Not provided</em>'}</td>
        </tr>
        <tr>
          <td class="label">Vehicle Type / Model</td>
          <td class="value">${vehicleDesc}</td>
        </tr>
        <tr>
          <td class="label">Service Required</td>
          <td class="value"><strong style="color:#ffffff;">${serviceName}</strong></td>
        </tr>
        <tr>
          <td class="label">Enquiry Source</td>
          <td class="value"><span style="background:#222; padding:3px 8px; border-radius:4px; font-size:12px;">${lead.source || 'Website Contact Form'}</span></td>
        </tr>
        <tr>
          <td class="label">Customer Message</td>
          <td class="value" style="color:#cccccc; font-style:italic;">"${lead.message || lead.notes || 'Direct lead submission from website'}"</td>
        </tr>
        <tr>
          <td class="label">Received At</td>
          <td class="value">${new Date().toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' })} (IST)</td>
        </tr>
      </table>

      <a href="https://thedriveclinic.com/crm#leads" class="crm-btn">View in Studio CRM Leads Pipeline &rarr;</a>
    </div>
    <div class="footer">
      This notification was automatically dispatched to <strong>${recipients.join(' & ')}</strong>.<br>
      The Drive Clinic &bull; 90 Nanak Nagar, Jammu, J&K &bull; +91 70061 34729
    </div>
  </div>
</body>
</html>
  `;

  return dispatchMultiChannelEmail({
    type: 'LEAD',
    recipients,
    replyTo: lead.email || 'Care@thedriveclinic.com',
    subject,
    html,
    text,
    relayFields: {
      Reference: leadRef,
      Customer_Name: customerName,
      Phone: lead.phone || 'N/A',
      WhatsApp_Link: waUrl || 'N/A',
      Email: lead.email || 'Not provided',
      Vehicle: vehicleDesc,
      Service_Requested: serviceName,
      Message: lead.message || lead.notes || 'Direct enquiry',
      Source: lead.source || 'Website'
    },
    rawPayload: lead
  });
}

/**
 * Send New Studio Appointment Booking Email to Care@thedriveclinic.com & thedriveclinic@gmail.com
 */
export async function sendBookingNotificationEmail(booking) {
  const recipients = getNotificationRecipients();
  const cleanPhone = (booking.customer?.phone || booking.phone || '').replace(/\D/g, '');
  const waUrl = cleanPhone ? `https://wa.me/${cleanPhone.startsWith('91') ? cleanPhone : '91' + cleanPhone}` : null;
  const bookingRef = booking.ref || `DRC-${Date.now().toString().slice(-5)}`;
  const customerName = booking.customer?.name || booking.customer_name || booking.name || 'Customer';
  const customerPhone = booking.customer?.phone || booking.customer_phone || booking.phone || 'N/A';
  const customerEmail = booking.customer?.email || booking.customer_email || booking.email || '';
  const serviceName = booking.service?.name || booking.service_name || booking.service || 'Studio Detailing';
  const vehicleDesc = [
    booking.vehicle?.brand || booking.vehicle_brand || '',
    booking.vehicle?.model || booking.vehicle_model || '',
    booking.vehicle?.year ? `(${booking.vehicle.year})` : '',
    booking.vehicle?.registration ? `• Reg: ${booking.vehicle.registration}` : ''
  ]
    .filter(Boolean)
    .join(' ') || 'Vehicle';

  const subject = `⚡ New Studio Booking Confirmed [${bookingRef}]: ${customerName} — ${serviceName} (${booking.date} at ${booking.time})`;

  const text = [
    `THE DRIVE CLINIC // STUDIO BOOKINGS // JAMMU`,
    `Target Mailboxes: ${recipients.join(', ')}`,
    `====================================================`,
    `EVENT: STUDIO BOOKING CONFIRMED`,
    `REFERENCE: ${bookingRef}`,
    `DATE & TIME: ${booking.date} at ${booking.time}`,
    `SERVICE: ${serviceName}`,
    `CUSTOMER: ${customerName}`,
    `PHONE: ${customerPhone} ${waUrl ? `(WhatsApp: ${waUrl})` : ''}`,
    `EMAIL: ${customerEmail || 'Not provided'}`,
    `VEHICLE: ${vehicleDesc}`,
    `NOTES: ${booking.notes || 'None specified'}`,
    `SOURCE: ${booking.source || 'Website Booking Engine'}`,
    `TIMESTAMP: ${new Date().toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' })} IST`,
    `====================================================`
  ].join('\n');

  const html = `
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { margin:0; padding:0; background:#0b0b0b; font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#f0f0f0; }
    .wrapper { max-width:620px; margin:20px auto; background:#141414; border:1px solid #282828; border-top:4px solid #E50914; border-radius:8px; overflow:hidden; }
    .header { padding:24px 30px; background:#111111; border-bottom:1px solid #222222; }
    .brand-title { font-size:24px; font-weight:900; letter-spacing:1px; color:#ffffff; margin:0; }
    .brand-tag { font-size:11px; font-weight:700; color:#E50914; letter-spacing:2px; text-transform:uppercase; margin-top:4px; }
    .content { padding:30px; }
    .alert-pill { display:inline-block; padding:5px 12px; background:rgba(34,197,94,0.15); border:1px solid #22c55e; color:#4ade80; border-radius:20px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:18px; }
    .lead-heading { font-size:20px; font-weight:700; color:#ffffff; margin:0 0 16px 0; }
    .table-details { width:100%; border-collapse:collapse; margin-bottom:24px; font-size:14px; }
    .table-details td { padding:10px 14px; border-bottom:1px solid #222222; }
    .table-details td.label { width:35%; color:#888888; font-weight:600; text-transform:uppercase; font-size:11.5px; letter-spacing:0.5px; }
    .table-details td.value { color:#ffffff; font-weight:500; }
    .wa-btn { display:inline-block; background:#25D366; color:#ffffff !important; text-decoration:none; padding:6px 14px; border-radius:4px; font-size:12px; font-weight:700; margin-left:8px; }
    .crm-btn { display:block; text-align:center; background:#E50914; color:#ffffff !important; text-decoration:none; padding:14px 24px; border-radius:6px; font-size:14px; font-weight:800; letter-spacing:1px; text-transform:uppercase; margin-top:20px; }
    .footer { padding:20px 30px; background:#0e0e0e; border-top:1px solid #222222; font-size:12px; color:#666666; text-align:center; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <div class="brand-title">THE TDC</div>
      <div class="brand-tag">THE DRIVE CLINIC // STUDIO BOOKINGS // JAMMU</div>
    </div>
    <div class="content">
      <span class="alert-pill">🚗 CONFIRMED STUDIO APPOINTMENT</span>
      <h2 class="lead-heading">New appointment booked via interactive booking engine:</h2>
      
      <table class="table-details">
        <tr>
          <td class="label">Booking Ref #</td>
          <td class="value"><strong style="color:#E50914; font-family:monospace; font-size:16px;">${bookingRef}</strong></td>
        </tr>
        <tr>
          <td class="label">Date & Time Slot</td>
          <td class="value"><strong style="color:#ffffff; font-size:15px;">${booking.date} at ${booking.time}</strong></td>
        </tr>
        <tr>
          <td class="label">Service Selected</td>
          <td class="value"><strong>${serviceName}</strong></td>
        </tr>
        <tr>
          <td class="label">Customer Name</td>
          <td class="value"><strong>${customerName}</strong></td>
        </tr>
        <tr>
          <td class="label">Phone / WhatsApp</td>
          <td class="value">
            <strong>${customerPhone}</strong>
            ${waUrl ? `<a href="${waUrl}" target="_blank" class="wa-btn">💬 WhatsApp</a>` : ''}
          </td>
        </tr>
        <tr>
          <td class="label">Email Address</td>
          <td class="value">${customerEmail || '<em>Not provided</em>'}</td>
        </tr>
        <tr>
          <td class="label">Vehicle Details</td>
          <td class="value">${vehicleDesc}</td>
        </tr>
        <tr>
          <td class="label">Special Notes</td>
          <td class="value" style="color:#cccccc;">${booking.notes || 'None specified'}</td>
        </tr>
        <tr>
          <td class="label">Booking Source</td>
          <td class="value"><span style="background:#222; padding:3px 8px; border-radius:4px; font-size:12px;">${booking.source || 'Website Booking Engine'}</span></td>
        </tr>
      </table>

      <a href="https://thedriveclinic.com/crm#bookings" class="crm-btn">Open in Studio Bookings & Bay Manager &rarr;</a>
    </div>
    <div class="footer">
      Dispatched automatically to <strong>${recipients.join(' & ')}</strong>.<br>
      The Drive Clinic &bull; 90 Nanak Nagar, Jammu, J&K &bull; +91 70061 34729
    </div>
  </div>
</body>
</html>
  `;

  return dispatchMultiChannelEmail({
    type: 'BOOKING',
    recipients,
    replyTo: customerEmail || 'Care@thedriveclinic.com',
    subject,
    html,
    text,
    relayFields: {
      Booking_Reference: bookingRef,
      Schedule_Date_Time: `${booking.date} at ${booking.time}`,
      Service_Selected: serviceName,
      Customer_Name: customerName,
      Phone: customerPhone,
      WhatsApp_Link: waUrl || 'N/A',
      Email: customerEmail || 'Not provided',
      Vehicle_Details: vehicleDesc,
      Special_Notes: booking.notes || 'None specified',
      Booking_Source: booking.source || 'Website Booking Engine'
    },
    rawPayload: booking
  });
}
