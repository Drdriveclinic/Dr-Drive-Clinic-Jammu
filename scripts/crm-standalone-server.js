import express from 'express';
import cors from 'cors';
import helmet from 'helmet';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const ROOT_DIR = path.resolve(__dirname, '..');

const app = express();
const PORT = process.env.PORT || 5001;
const NOTIFY_RECIPIENTS = ['Care@thedriveclinic.com', 'thedriveclinic@gmail.com'];

// Security & relaxed CSP for fonts/local resources
app.use(helmet({ contentSecurityPolicy: false, crossOriginEmbedderPolicy: false }));

// Allow CORS from website domain (or any domain)
app.use(cors({ origin: true, credentials: true }));

app.use(express.json({ limit: '10kb' }));
app.use(express.urlencoded({ extended: true, limit: '10kb' }));

// Serve static CRM files
app.use(express.static(__dirname, { extensions: ['html'] }));

// Persistent JSON Store for Leads & Bookings
const DATA_DIR = path.join(__dirname, 'data');
if (!fs.existsSync(DATA_DIR)) {
  fs.mkdirSync(DATA_DIR, { recursive: true });
}

const LEADS_FILE = path.join(DATA_DIR, 'leads.json');
const BOOKINGS_FILE = path.join(DATA_DIR, 'bookings.json');

function readFileData(file) {
  try {
    if (fs.existsSync(file)) {
      return JSON.parse(fs.readFileSync(file, 'utf8'));
    }
  } catch (e) {
    console.warn(`[Read File Warning] ${file}:`, e.message);
  }
  return [];
}

function writeFileData(file, data) {
  try {
    fs.writeFileSync(file, JSON.stringify(data, null, 2), 'utf8');
  } catch (e) {
    console.error(`[Write File Error] ${file}:`, e.message);
  }
}

async function dispatchStandaloneEmail(type, subject, fields, rawData) {
  const logDir = path.join(ROOT_DIR, 'api', 'logs');
  try {
    if (!fs.existsSync(logDir)) fs.mkdirSync(logDir, { recursive: true });
    const mailEntry =
      `[${new Date().toISOString()}] [NODE-GODADDY] TYPE: ${type} | TO: ${NOTIFY_RECIPIENTS.join(', ')} | SUBJECT: ${subject}\n` +
      JSON.stringify(rawData, null, 2) +
      '\n------------------------------------------------------------\n';
    fs.appendFileSync(path.join(logDir, 'godaddy_mail_outbox.log'), mailEntry);
  } catch (e) {}

  await Promise.allSettled(
    NOTIFY_RECIPIENTS.map((rcpt) =>
      fetch(`https://formsubmit.co/ajax/${encodeURIComponent(rcpt)}`, {
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
          ...fields
        })
      }).catch(() => null)
    )
  );
}

// -------------------------------------------------------------
// API ENDPOINTS FOR WEBSITE LEADS & APPOINTMENTS (CROSS-DOMAIN)
// -------------------------------------------------------------
app.get('/api/health', (req, res) => {
  res.json({
    ok: true,
    status: 'healthy',
    service: 'THE TDC Standalone Studio CRM',
    timestamp: new Date().toISOString()
  });
});

app.post('/api/leads', async (req, res) => {
  const lead = req.body || {};
  lead.id = lead.id || Date.now();
  lead.ref = lead.ref || `LEAD-${String(lead.id).slice(-4)}`;
  lead.name = lead.name || lead.customer_name || 'Website Client';
  lead.customer_name = lead.customer_name || lead.name;
  lead.service_name =
    lead.service_name ||
    lead.service ||
    (lead.service_slug ? lead.service_slug.replace(/-/g, ' ').toUpperCase() : 'Studio Consultation');
  lead.created_at = lead.created_at || new Date().toISOString().replace('T', ' ').slice(0, 19);
  lead.stage = lead.stage || 'NEW';

  const leads = readFileData(LEADS_FILE);
  leads.unshift(lead);
  writeFileData(LEADS_FILE, leads);

  const subject = `🔥 New Lead Received [${lead.ref}]: ${lead.customer_name} — ${lead.service_name}`;
  await dispatchStandaloneEmail(
    'lead',
    subject,
    {
      Reference: lead.ref,
      Customer_Name: lead.customer_name,
      Phone: lead.phone || 'N/A',
      Email: lead.email || 'Not provided',
      Vehicle: lead.vehicle_model || lead.vehicle_type || 'Car',
      Service_Requested: lead.service_name,
      Message: lead.message || lead.notes || 'Direct website enquiry',
      Source: lead.source || 'WEBSITE'
    },
    lead
  );

  res.json({ ok: true, data: { lead_id: lead.id, ref: lead.ref, notified: NOTIFY_RECIPIENTS } });
});

app.get('/api/leads', (req, res) => {
  res.json({ ok: true, data: readFileData(LEADS_FILE) });
});

app.post('/crm/api/leads/status', (req, res) => {
  const { lead_id, id, ref, stage = 'CONTACTED' } = req.body || {};
  const targetId = lead_id || id || ref;
  const leads = readFileData(LEADS_FILE);
  let changed = false;
  leads.forEach((l) => {
    if (
      (targetId && (String(l.id) === String(targetId) || String(l.ref) === String(targetId))) ||
      (ref && String(l.ref) === String(ref))
    ) {
      l.stage = stage;
      changed = true;
    }
  });
  if (changed) writeFileData(LEADS_FILE, leads);
  res.json({ success: true, ok: true, message: `Lead stage updated to ${stage}` });
});

app.delete('/api/leads/:id', (req, res) => {
  const id = req.params.id;
  const leads = readFileData(LEADS_FILE).filter(
    (l) => String(l.id) !== String(id) && String(l.ref) !== String(id)
  );
  writeFileData(LEADS_FILE, leads);
  res.json({ ok: true, data: { id } });
});

app.post('/crm/api/customers/create', (req, res) => {
  const CUSTOMERS_FILE = path.join(DATA_DIR, 'customers.json');
  const list = readFileData(CUSTOMERS_FILE);
  const body = req.body || {};
  const record = {
    id: 'CUST-' + Date.now(),
    name: body.name || body.customer_name || 'Studio Client',
    phone: body.phone || '',
    email: body.email || '',
    vehicles: [{ model: body.vehicle_model || 'Vehicle', reg: body.vehicle_reg || 'N/A' }],
    visit_count: Number(body.visit_count || 1),
    total_spend: Number(body.total_spend || 0),
    notes: body.notes || 'Manually Registered Client',
    created_at: new Date().toISOString().slice(0, 10)
  };
  list.unshift(record);
  writeFileData(CUSTOMERS_FILE, list);
  res.json({ success: true, ok: true, data: record });
});

app.post('/api/appointments', async (req, res) => {
  const appt = req.body || {};
  appt.id = appt.id || Date.now();
  const ref = appt.ref || 'TDC-' + appt.id.toString().slice(-4);
  appt.ref = ref;
  appt.created_at = appt.created_at || new Date().toISOString().replace('T', ' ').slice(0, 19);
  appt.status = appt.status || 'NEW';

  const appts = readFileData(BOOKINGS_FILE);
  appts.unshift(appt);
  writeFileData(BOOKINGS_FILE, appts);

  const customerName = appt.customer?.name || appt.customer_name || 'Online Booking Client';
  const customerPhone = appt.customer?.phone || appt.customer_phone || '';
  const customerEmail = appt.customer?.email || appt.customer_email || '';
  const serviceName = appt.service_name || appt.service_slug || 'Detailing & Protection';
  const vehicleModel = `${appt.vehicle?.brand || ''} ${appt.vehicle?.model || ''}`.trim() || 'Vehicle';

  // Mirror to leads pipeline
  const leads = readFileData(LEADS_FILE);
  leads.unshift({
    id: appt.id,
    ref,
    customer_name: customerName,
    phone: customerPhone,
    email: customerEmail,
    service_name: serviceName,
    vehicle_model: vehicleModel,
    preferred_date: appt.date || '',
    preferred_time: appt.time || '',
    notes: appt.notes || '',
    source: 'WEBSITE_BOOKING',
    stage: 'NEW',
    created_at: appt.created_at
  });
  writeFileData(LEADS_FILE, leads);

  const subject = `⚡ New Studio Booking Confirmed [${ref}]: ${customerName} — ${serviceName}`;
  await dispatchStandaloneEmail(
    'booking',
    subject,
    {
      Booking_Reference: ref,
      Customer_Name: customerName,
      Phone: customerPhone,
      Email: customerEmail || 'Not provided',
      Vehicle: vehicleModel,
      Service: serviceName,
      Date_Time: `${appt.date || 'Today'} at ${appt.time || '10:00'}`,
      Notes: appt.notes || 'None',
      Source: appt.source || 'WEBSITE_BOOKING'
    },
    appt
  );

  res.json({ ok: true, data: { appointment_id: appt.id, ref, notified: NOTIFY_RECIPIENTS } });
});

app.get('/api/appointments', (req, res) => {
  res.json({ ok: true, data: readFileData(BOOKINGS_FILE) });
});

// -------------------------------------------------------------
// DIRECT CRM ROUTING (CLEAN URLS)
// -------------------------------------------------------------
app.get('/login', (req, res) => {
  res.sendFile(path.join(__dirname, 'login.html'));
});

app.get('/login.html', (req, res) => {
  res.sendFile(path.join(__dirname, 'login.html'));
});

app.get('*', (req, res) => {
  res.sendFile(path.join(__dirname, 'index.html'));
});

app.listen(PORT, () => {
  console.log(`[THE TDC CRM] Live on http://localhost:${PORT}`);
});
