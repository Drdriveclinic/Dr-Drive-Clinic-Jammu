import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import db from '../db.js';
import { notify } from '../services/notify.service.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const DATA_DIR = path.resolve(__dirname, '../../data');
const LEADS_FILE = path.join(DATA_DIR, 'leads.json');

// Ensure data directory exists
try {
  if (!fs.existsSync(DATA_DIR)) {
    fs.mkdirSync(DATA_DIR, { recursive: true });
  }
} catch (e) {}

function getFileLeads() {
  try {
    if (fs.existsSync(LEADS_FILE)) {
      const content = fs.readFileSync(LEADS_FILE, 'utf8');
      return JSON.parse(content);
    }
  } catch (e) {}
  return [];
}

function saveFileLead(lead) {
  try {
    const list = getFileLeads();
    list.unshift(lead);
    fs.writeFileSync(LEADS_FILE, JSON.stringify(list, null, 2), 'utf8');
  } catch (e) {
    console.error('[Save Lead File Error]:', e.message);
  }
}

export async function createLead(req, res, next) {
  try {
    const {
      source = 'contact',
      name,
      phone,
      email,
      vehicle_type = 'car',
      vehicle_model,
      vehicle_reg,
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
    } else if (service_slug) {
      serviceName = service_slug.replace(/-/g, ' ').toUpperCase();
    }

    const customerData = {
      name,
      phone,
      email: email || null,
      vehicle_type,
      vehicle_model: vehicle_model || vehicle_type,
      vehicle_reg: vehicle_reg || null
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

    const leadRecord = {
      id: leadId,
      ref: `LEAD-${leadId}`,
      name,
      customer_name: name,
      phone,
      email: email || null,
      vehicle_type,
      vehicle_model: vehicle_model || (vehicle_type === 'bike' ? 'Superbike' : 'Car'),
      vehicle_reg: vehicle_reg || null,
      service_slug: service_slug || null,
      service_name: serviceName,
      source,
      message: message || `Direct ${source} submission from website`,
      stage: 'NEW',
      created_at: new Date().toISOString().replace('T', ' ').slice(0, 19)
    };

    // Save to persistent file store
    saveFileLead(leadRecord);

    // Trigger Notification to GoDaddy Business Mail (care@thedriveclinic.com)
    await notify({
      type: 'lead',
      ref: `LEAD-${leadId}`,
      customer: customerData,
      service: serviceName,
      message,
      source,
      date: new Date().toISOString().split('T')[0],
      time: new Date().toLocaleTimeString('en-GB')
    });

    res.status(201).json({
      ok: true,
      data: {
        lead_id: leadId,
        ref: `LEAD-${leadId}`,
        message: 'Your enquiry has been received. Our studio team will get in touch shortly via WhatsApp.'
      }
    });
  } catch (err) {
    next(err);
  }
}

export async function getLeads(req, res, next) {
  try {
    const isDbReady = await db.checkConnection();
    if (isDbReady) {
      const rows = await db.query('SELECT * FROM leads ORDER BY created_at DESC');
      const formatted = rows.map((r) => {
        let cust = {};
        try {
          cust = typeof r.customer_json === 'string' ? JSON.parse(r.customer_json) : r.customer_json || {};
        } catch (e) {}
        return {
          id: r.id,
          ref: r.lead_no || `LEAD-${r.id}`,
          name: r.customer_name || cust.name || 'Client',
          customer_name: r.customer_name || cust.name || 'Client',
          phone: r.phone || cust.phone || '',
          email: r.email || cust.email || '',
          vehicle_model: r.vehicle_model || cust.vehicle_model || cust.vehicle_type || 'Vehicle',
          vehicle_reg: r.vehicle_reg || cust.registration || '',
          service_name: r.service_name || 'Studio Service',
          source: r.source || 'website',
          stage: r.stage || r.status || 'NEW',
          created_at: r.created_at
        };
      });
      return res.json({ ok: true, data: formatted });
    }

    // Fallback to persistent file
    const fileLeads = getFileLeads();
    res.json({ ok: true, data: fileLeads });
  } catch (err) {
    next(err);
  }
}

export async function bulkImportLeads(req, res, next) {
  try {
    const rawLeads = req.body?.leads;
    if (!Array.isArray(rawLeads) || rawLeads.length === 0) {
      return res.status(400).json({ ok: false, error: { message: 'Invalid or empty leads array' } });
    }

    const currentLeads = getFileLeads();
    const imported = [];

    for (const item of rawLeads) {
      const id = item.id || Math.floor(10000 + Math.random() * 90000);
      const leadRecord = {
        id,
        ref: item.ref || `LEAD-${id}`,
        name: item.customer_name || item.name || 'Ad Lead',
        customer_name: item.customer_name || item.name || 'Ad Lead',
        phone: item.phone || '',
        email: item.email || null,
        vehicle_type: item.vehicle_type || 'car',
        vehicle_model: item.vehicle_model || item.vehicle || 'Vehicle',
        vehicle_reg: item.vehicle_reg || 'N/A',
        service_slug: item.service_slug || null,
        service_name: item.service_name || item.service || 'Studio Consultation',
        source: (item.source || 'ADS_CAMPAIGN').toUpperCase(),
        message: item.message || item.notes || 'Imported from Ads spreadsheet',
        stage: item.stage || 'NEW',
        created_at: item.created_at || new Date().toISOString().replace('T', ' ').slice(0, 19)
      };
      imported.push(leadRecord);
      currentLeads.unshift(leadRecord);
    }

    fs.writeFileSync(LEADS_FILE, JSON.stringify(currentLeads, null, 2), 'utf8');

    res.status(201).json({
      ok: true,
      data: {
        count: imported.length,
        leads: imported
      }
    });
  } catch (err) {
    next(err);
  }
}

export async function deleteLead(req, res, next) {
  try {
    const id = req.params.id;
    const currentLeads = getFileLeads();
    const updatedLeads = currentLeads.filter(l => String(l.id) !== String(id) && String(l.ref) !== String(id));
    fs.writeFileSync(LEADS_FILE, JSON.stringify(updatedLeads, null, 2), 'utf8');

    const isDbReady = await db.checkConnection();
    if (isDbReady) {
      await db.query('DELETE FROM leads WHERE id = ?', [id]);
    }

    res.json({ ok: true, data: { message: 'Lead deleted successfully', id } });
  } catch (err) {
    next(err);
  }
}
