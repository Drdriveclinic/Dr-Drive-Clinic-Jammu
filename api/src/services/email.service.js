import nodemailer from 'nodemailer';
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

/**
 * Creates Nodemailer Transporter configured for GoDaddy Business Email
 */
function createTransporter() {
  if (!env.SMTP_PASS) {
    return null;
  }

  return nodemailer.createTransport({
    host: env.SMTP_HOST || 'smtpout.secureserver.net',
    port: Number(env.SMTP_PORT) || 465,
    secure: env.SMTP_SECURE !== false, // true for port 465, false for 587
    auth: {
      user: env.SMTP_USER || 'care@thedriveclinic.com',
      pass: env.SMTP_PASS
    },
    tls: {
      rejectUnauthorized: false
    }
  });
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
 * Send New Website Lead Email to GoDaddy Care Inbox (care@thedriveclinic.com)
 */
export async function sendLeadNotificationEmail(lead) {
  const recipient = env.NOTIFICATION_EMAIL || 'care@thedriveclinic.com';
  const cleanPhone = (lead.phone || '').replace(/\D/g, '');
  const waUrl = cleanPhone ? `https://wa.me/${cleanPhone.startsWith('91') ? cleanPhone : '91' + cleanPhone}` : null;
  const leadRef = lead.ref || `LEAD-${lead.lead_id || Date.now().toString().slice(-4)}`;

  const subject = `🔥 New Lead Received [${leadRef}]: ${lead.name} — ${lead.service || 'Service Enquiry'}`;

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
      <div class="brand-title">THE DDC</div>
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
          <td class="value"><strong>${lead.name || 'Unknown'}</strong></td>
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
          <td class="value">${lead.vehicle_type || 'Car'} ${lead.vehicle_model ? `(${lead.vehicle_model})` : ''}</td>
        </tr>
        <tr>
          <td class="label">Service Required</td>
          <td class="value"><strong style="color:#ffffff;">${lead.service || lead.service_slug || 'General Consultation'}</strong></td>
        </tr>
        <tr>
          <td class="label">Enquiry Source</td>
          <td class="value"><span style="background:#222; padding:3px 8px; border-radius:4px; font-size:12px;">${lead.source || 'Website Contact Form'}</span></td>
        </tr>
        <tr>
          <td class="label">Customer Message</td>
          <td class="value" style="color:#cccccc; font-style:italic;">"${lead.message || 'Direct lead submission from website'}"</td>
        </tr>
        <tr>
          <td class="label">Received At</td>
          <td class="value">${new Date().toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' })} (IST)</td>
        </tr>
      </table>

      <a href="https://thedriveclinic.com/crm#leads" class="crm-btn">View in Studio CRM Leads Pipeline &rarr;</a>
    </div>
    <div class="footer">
      This notification was automatically dispatched to <strong>care@thedriveclinic.com</strong> via GoDaddy Business Mail.<br>
      The Drive Clinic &bull; 90 Nanak Nagar, Jammu, J&K &bull; +91 94191 00000
    </div>
  </div>
</body>
</html>
  `;

  // Always log to audit
  logEmailToAudit('LEAD', recipient, subject, lead);

  const transporter = createTransporter();
  if (!transporter) {
    console.log('----------------------------------------------------');
    console.log(`[GoDaddy Email Notification Queued] (Awaiting SMTP password in .env)`);
    console.log(`- Recipient: ${recipient}`);
    console.log(`- Subject:   ${subject}`);
    console.log(`- Customer:  ${lead.name} (${lead.phone})`);
    console.log(`- Service:   ${lead.service}`);
    console.log('----------------------------------------------------');
    return { success: true, delivered: false, queued: true, recipient };
  }

  try {
    const info = await transporter.sendMail({
      from: env.NOTIFICATION_FROM || '"The Drive Clinic" <care@thedriveclinic.com>',
      to: recipient,
      subject,
      html
    });
    console.log(`[GoDaddy Email Delivered]: ${info.messageId} to ${recipient}`);
    return { success: true, delivered: true, messageId: info.messageId, recipient };
  } catch (err) {
    console.error(`[GoDaddy SMTP Delivery Error]:`, err.message);
    return { success: false, error: err.message, recipient };
  }
}

/**
 * Send New Studio Appointment Booking Email to GoDaddy Care Inbox (care@thedriveclinic.com)
 */
export async function sendBookingNotificationEmail(booking) {
  const recipient = env.NOTIFICATION_EMAIL || 'care@thedriveclinic.com';
  const cleanPhone = (booking.customer?.phone || '').replace(/\D/g, '');
  const waUrl = cleanPhone ? `https://wa.me/${cleanPhone.startsWith('91') ? cleanPhone : '91' + cleanPhone}` : null;
  const bookingRef = booking.ref || `DRC-${Date.now().toString().slice(-5)}`;

  const subject = `⚡ New Studio Booking Confirmed [${bookingRef}]: ${booking.customer?.name} — ${booking.service?.name || booking.service} (${booking.date} at ${booking.time})`;

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
      <div class="brand-title">THE DDC</div>
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
          <td class="value"><strong>${booking.service?.name || booking.service || 'Studio Detailing'}</strong></td>
        </tr>
        <tr>
          <td class="label">Customer Name</td>
          <td class="value"><strong>${booking.customer?.name || 'Customer'}</strong></td>
        </tr>
        <tr>
          <td class="label">Phone / WhatsApp</td>
          <td class="value">
            <strong>${booking.customer?.phone || 'N/A'}</strong>
            ${waUrl ? `<a href="${waUrl}" target="_blank" class="wa-btn">💬 WhatsApp</a>` : ''}
          </td>
        </tr>
        <tr>
          <td class="label">Email Address</td>
          <td class="value">${booking.customer?.email || '<em>Not provided</em>'}</td>
        </tr>
        <tr>
          <td class="label">Vehicle Details</td>
          <td class="value">
            ${booking.vehicle?.brand || ''} ${booking.vehicle?.model || ''}
            ${booking.vehicle?.year ? `(${booking.vehicle.year})` : ''}
            ${booking.vehicle?.registration ? `&bull; Reg: ${booking.vehicle.registration}` : ''}
            ${booking.vehicle?.color ? `&bull; Color: ${booking.vehicle.color}` : ''}
          </td>
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
      Dispatched automatically to <strong>care@thedriveclinic.com</strong> via GoDaddy Business Mail.<br>
      The Drive Clinic &bull; 90 Nanak Nagar, Jammu, J&K &bull; +91 94191 00000
    </div>
  </div>
</body>
</html>
  `;

  // Always log to audit
  logEmailToAudit('BOOKING', recipient, subject, booking);

  const transporter = createTransporter();
  if (!transporter) {
    console.log('----------------------------------------------------');
    console.log(`[GoDaddy Email Notification Queued] (Awaiting SMTP password in .env)`);
    console.log(`- Recipient: ${recipient}`);
    console.log(`- Subject:   ${subject}`);
    console.log(`- Customer:  ${booking.customer?.name} (${booking.customer?.phone})`);
    console.log(`- Slot:      ${booking.date} at ${booking.time}`);
    console.log('----------------------------------------------------');
    return { success: true, delivered: false, queued: true, recipient };
  }

  try {
    const info = await transporter.sendMail({
      from: env.NOTIFICATION_FROM || '"The Drive Clinic" <care@thedriveclinic.com>',
      to: recipient,
      subject,
      html
    });
    console.log(`[GoDaddy Email Delivered]: ${info.messageId} to ${recipient}`);
    return { success: true, delivered: true, messageId: info.messageId, recipient };
  } catch (err) {
    console.error(`[GoDaddy SMTP Delivery Error]:`, err.message);
    return { success: false, error: err.message, recipient };
  }
}
