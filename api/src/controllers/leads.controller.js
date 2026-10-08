import db from '../db.js';
import { notify } from '../services/notify.service.js';

export async function createLead(req, res, next) {
  try {
    const {
      source = 'contact',
      name,
      phone,
      email,
      vehicle_type = 'car',
      service_slug,
      message,
      utm_params
    } = req.validatedBody;

    const isDbReady = await db.checkConnection();
    let serviceId = null;
    let serviceName = 'General Inquiry';

    if (isDbReady && service_slug) {
      const rows = await db.query('SELECT id, name FROM services WHERE slug = ?', [service_slug]);
      if (rows.length > 0) {
        serviceId = rows[0].id;
        serviceName = rows[0].name;
      }
    }

    const customerData = {
      name,
      phone,
      email: email || null,
      vehicle_type
    };

    let leadId = Math.floor(1000 + Math.random() * 9000);

    if (isDbReady) {
      const result = await db.query(
        `INSERT INTO leads (source, customer_json, service_id, message, status, utm_json)
         VALUES (?, ?, ?, ?, 'new', ?)`,
        [
          source,
          JSON.stringify(customerData),
          serviceId,
          message || `Direct ${source} submission from website`,
          utm_params ? JSON.stringify(utm_params) : null
        ]
      );
      leadId = result.insertId;
    }

    // Trigger Notification Stub
    await notify({
      type: `lead (${source})`,
      ref: `LEAD-${leadId}`,
      customer: customerData,
      service: serviceName,
      date: new Date().toISOString().split('T')[0],
      time: new Date().toLocaleTimeString('en-GB')
    });

    res.status(201).json({
      ok: true,
      data: {
        lead_id: leadId,
        message: 'Your enquiry has been received. Our studio team will get in touch shortly via WhatsApp.'
      }
    });
  } catch (err) {
    next(err);
  }
}
