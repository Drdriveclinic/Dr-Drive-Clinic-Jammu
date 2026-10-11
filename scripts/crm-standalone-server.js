import express from 'express';
import cors from 'cors';
import helmet from 'helmet';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = process.env.PORT || 5001;

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

// -------------------------------------------------------------
// API ENDPOINTS FOR WEBSITE LEADS & APPOINTMENTS (CROSS-DOMAIN)
// -------------------------------------------------------------
app.get('/api/health', (req, res) => {
  res.json({
    ok: true,
    status: 'healthy',
    service: 'THE DDC Standalone Studio CRM',
    timestamp: new Date().toISOString()
  });
});

app.post('/api/leads', (req, res) => {
  const lead = req.body;
  lead.id = lead.id || Date.now();
  lead.created_at = lead.created_at || new Date().toISOString().replace('T', ' ').slice(0, 19);
  lead.stage = lead.stage || 'NEW';

  const leads = readFileData(LEADS_FILE);
  leads.unshift(lead);
  writeFileData(LEADS_FILE, leads);

  res.json({ ok: true, data: { lead_id: lead.id } });
});

app.get('/api/leads', (req, res) => {
  res.json({ ok: true, data: readFileData(LEADS_FILE) });
});

app.post('/api/appointments', (req, res) => {
  const appt = req.body;
  appt.id = appt.id || Date.now();
  appt.created_at = appt.created_at || new Date().toISOString().replace('T', ' ').slice(0, 19);
  appt.status = appt.status || 'NEW';

  const appts = readFileData(BOOKINGS_FILE);
  appts.unshift(appt);
  writeFileData(BOOKINGS_FILE, appts);

  // Also mirror to leads pipeline
  const leads = readFileData(LEADS_FILE);
  leads.unshift({
    id: appt.id,
    customer_name: appt.customer?.name || 'Online Booking Client',
    phone: appt.customer?.phone || '',
    email: appt.customer?.email || '',
    service_name: appt.service_slug || 'Detailing & Protection',
    vehicle_model: `${appt.vehicle?.brand || ''} ${appt.vehicle?.model || ''}`.trim(),
    preferred_date: appt.date || '',
    preferred_time: appt.time || '',
    notes: appt.notes || '',
    source: 'WEBSITE_BOOKING',
    stage: 'NEW',
    created_at: appt.created_at
  });
  writeFileData(LEADS_FILE, leads);

  res.json({ ok: true, data: { appointment_id: appt.id, ref: 'DDC-' + appt.id.toString().slice(-4) } });
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
  console.log(`[THE DDC CRM] Live on http://localhost:${PORT}`);
});
