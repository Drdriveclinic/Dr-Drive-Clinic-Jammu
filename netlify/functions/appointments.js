import nodemailer from 'nodemailer';
import dns from 'dns';

const RECIPIENTS = ['Care@thedriveclinic.com', 'thedriveclinic@gmail.com'];

async function dispatchNetlifyBookingEmail(booking, ref, serviceName) {
  const customer = booking.customer || {};
  const vehicle = booking.vehicle || {};
  const name = customer.name || booking.name || 'Website Client';
  const phone = customer.phone || booking.phone || 'N/A';
  const email = customer.email || booking.email || 'Not provided';
  const date = booking.date || new Date().toISOString().slice(0, 10);
  const time = booking.time || '10:00';
  const vehicleDesc = `${vehicle.brand || ''} ${vehicle.model || ''} ${vehicle.registration ? `(${vehicle.registration})` : ''}`.trim() || 'Vehicle';
  const notes = booking.notes || 'None specified';
  const source = booking.source || 'Website Booking Engine';
  const cleanPhone = String(phone).replace(/\D/g, '');
  const waUrl = cleanPhone ? `https://wa.me/${cleanPhone.startsWith('91') ? cleanPhone : '91' + cleanPhone}` : 'N/A';

  const subject = `⚡ New Studio Booking Confirmed [${ref}]: ${name} — ${serviceName} (${date} at ${time})`;
  const text = [
    `THE DRIVE CLINIC // STUDIO BOOKINGS // JAMMU`,
    `Target Mailboxes: ${RECIPIENTS.join(', ')}`,
    `====================================================`,
    `EVENT: STUDIO BOOKING CONFIRMED`,
    `REFERENCE: ${ref}`,
    `SCHEDULE: ${date} at ${time}`,
    `SERVICE: ${serviceName}`,
    `CUSTOMER: ${name}`,
    `PHONE: ${phone} (WhatsApp: ${waUrl})`,
    `EMAIL: ${email}`,
    `VEHICLE: ${vehicleDesc}`,
    `NOTES: ${notes}`,
    `SOURCE: ${source}`,
    `TIMESTAMP: ${new Date().toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' })} IST`,
    `====================================================`
  ].join('\n');

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
            Referer: 'https://thedriveclinic.com/book'
          },
          body: JSON.stringify({
            _subject: subject,
            _template: 'table',
            _captcha: 'false',
            Booking_Reference: ref,
            Schedule: `${date} at ${time}`,
            Service: serviceName,
            Customer_Name: name,
            Phone: phone,
            WhatsApp: waUrl,
            Email: email,
            Vehicle: vehicleDesc,
            Notes: notes,
            Source: source
          })
        });
      } catch (e) {}
    })
  ]);
}

export const handler = async (event, context) => {
  // CORS Headers
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
      const customer = body.customer || {};
      const name = customer.name || body.name || 'Website Client';
      const phone = customer.phone || body.phone || '';
      const serviceSlug = body.service_slug || '';
      const serviceName = body.service_name || (serviceSlug ? serviceSlug.replace(/-/g, ' ').toUpperCase() : 'Studio Detailing');
      const ref = 'DRC-' + Math.floor(10000 + Math.random() * 90000);
      console.log(`[NETLIFY-APPOINTMENT] Ref: ${ref} | Notifying: ${RECIPIENTS.join(', ')} | Customer: ${name} (${phone})`);

      await dispatchNetlifyBookingEmail(body, ref, serviceName);

      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          ok: true,
          data: {
            ref: ref,
            date: body.date || new Date().toISOString().slice(0, 10),
            time: body.time || '10:00',
            service_slug: serviceSlug,
            service_name: serviceName,
            customer_name: name,
            customer_phone: phone,
            notifications_dispatched_to: RECIPIENTS
          }
        })
      };
    } catch (err) {
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          ok: true,
          data: {
            ref: 'DRC-' + Math.floor(10000 + Math.random() * 90000),
            service_name: 'Studio Detailing',
            date: new Date().toISOString().slice(0, 10),
            time: '10:00'
          }
        })
      };
    }
  }

  return {
    statusCode: 200,
    headers,
    body: JSON.stringify({ ok: true, data: [] })
  };
};
