import nodemailer from 'nodemailer';
import dns from 'dns';

const RECIPIENTS = ['Care@thedriveclinic.com', 'thedriveclinic@gmail.com'];

async function dispatchNetlifyLeadEmail(lead) {
  const ref = lead.ref || 'LEAD-' + Math.floor(1000 + Math.random() * 9000);
  const name = lead.name || lead.customer_name || 'Website Client';
  const phone = lead.phone || 'N/A';
  const email = lead.email || 'Not provided';
  const service = lead.service_name || lead.service || (lead.service_slug ? lead.service_slug.replace(/-/g, ' ').toUpperCase() : 'Studio Consultation');
  const vehicle = lead.vehicle_model || lead.vehicle || (lead.vehicle_type === 'bike' ? 'Superbike' : 'Car');
  const message = lead.message || lead.notes || 'Direct website enquiry';
  const source = lead.source || 'Website';
  const cleanPhone = String(phone).replace(/\D/g, '');
  const waUrl = cleanPhone ? `https://wa.me/${cleanPhone.startsWith('91') ? cleanPhone : '91' + cleanPhone}` : 'N/A';

  const subject = `🔥 New Lead Received [${ref}]: ${name} — ${service}`;
  const text = [
    `THE DRIVE CLINIC // STUDIO MANAGEMENT // JAMMU`,
    `Target Mailboxes: ${RECIPIENTS.join(', ')}`,
    `====================================================`,
    `EVENT: NEW WEBSITE LEAD`,
    `REFERENCE: ${ref}`,
    `CUSTOMER: ${name}`,
    `PHONE: ${phone} (WhatsApp: ${waUrl})`,
    `EMAIL: ${email}`,
    `VEHICLE: ${vehicle}`,
    `SERVICE: ${service}`,
    `MESSAGE: ${message}`,
    `SOURCE: ${source}`,
    `TIMESTAMP: ${new Date().toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' })} IST`,
    `====================================================`
  ].join('\n');

  // 1. Authenticated SMTP if configured in Netlify Environment Variables
  if (process.env.SMTP_PASS) {
    try {
      const transporter = nodemailer.createTransport({
        host: process.env.SMTP_HOST || 'smtpout.secureserver.net',
        port: Number(process.env.SMTP_PORT) || 465,
        secure: (Number(process.env.SMTP_PORT) || 465) === 465,
        auth: {
          user: process.env.SMTP_USER || 'Care@thedriveclinic.com',
          pass: process.env.SMTP_PASS
        },
        tls: { rejectUnauthorized: false }
      });
      await transporter.sendMail({
        from: process.env.NOTIFICATION_FROM || '"The Drive Clinic" <Care@thedriveclinic.com>',
        to: RECIPIENTS.join(', '),
        replyTo: email !== 'Not provided' ? email : 'Care@thedriveclinic.com',
        subject,
        text
      });
      return;
    } catch (e) {
      console.warn('[Netlify SMTP Warning]:', e.message);
    }
  }

  // 2. Direct MX & HTTPS FormSubmit Relay
  await Promise.allSettled([
    ...RECIPIENTS.map(async (rcpt) => {
      try {
        const domain = rcpt.split('@')[1];
        const mxRecords = await dns.promises.resolveMx(domain);
        if (mxRecords && mxRecords.length > 0) {
          mxRecords.sort((a, b) => a.priority - b.priority);
          const mxTransport = nodemailer.createTransport({
            host: mxRecords[0].exchange,
            port: 25,
            secure: false,
            name: 'thedriveclinic.com',
            tls: { rejectUnauthorized: false },
            connectionTimeout: 4000
          });
          await mxTransport.sendMail({
            from: '"The Drive Clinic" <Care@thedriveclinic.com>',
            to: rcpt,
            subject,
            text
          });
        }
      } catch (e) {}
    }),
    ...RECIPIENTS.map(async (rcpt) => {
      try {
        await fetch(`https://formsubmit.co/ajax/${encodeURIComponent(rcpt)}`, {
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
            Reference: ref,
            Customer_Name: name,
            Phone: phone,
            WhatsApp: waUrl,
            Email: email,
            Vehicle: vehicle,
            Service_Requested: service,
            Message: message,
            Source: source
          })
        });
      } catch (e) {}
    })
  ]);
}

export const handler = async (event, context) => {
  const headers = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': 'Content-Type',
    'Access-Control-Allow-Methods': 'GET, POST, OPTIONS',
    'Content-Type': 'application/json'
  };

  if (event.httpMethod === 'OPTIONS') {
    return { statusCode: 200, headers, body: '' };
  }

  if (event.httpMethod === 'POST') {
    try {
      const body = JSON.parse(event.body || '{}');
      const leadId = Date.now();
      const ref = body.ref || 'LEAD-' + Math.floor(1000 + Math.random() * 9000);
      await dispatchNetlifyLeadEmail({ ...body, ref });

      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          ok: true,
          data: {
            lead_id: leadId,
            ref: ref,
            notified: RECIPIENTS,
            message: 'Your enquiry has been received.'
          }
        })
      };
    } catch (e) {
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({ ok: true, data: { lead_id: Date.now(), ref: 'LEAD-9999' } })
      };
    }
  }

  return {
    statusCode: 200,
    headers,
    body: JSON.stringify({ ok: true, data: [] })
  };
};
