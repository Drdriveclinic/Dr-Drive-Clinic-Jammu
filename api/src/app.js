import express from 'express';
import helmet from 'helmet';
import cors from 'cors';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { env } from './config/env.js';
import routes from './routes/index.js';
import { errorHandler } from './middleware/error.middleware.js';
import { generalLimiter } from './middleware/rateLimiter.middleware.js';
import { notify } from './services/notify.service.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const DIST_DIR = path.resolve(__dirname, '../../dist');

const app = express();

// Security Headers (relaxed CSP to permit Google Fonts & local assets)
app.use(
  helmet({
    contentSecurityPolicy: false,
    crossOriginEmbedderPolicy: false
  })
);

// CORS configuration
const corsOptions = {
  origin: env.CORS_ORIGIN === '*' ? true : [env.CORS_ORIGIN, 'http://localhost:8000', 'http://127.0.0.1:8000'],
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization'],
  credentials: true
};
app.use(cors(corsOptions));

// Body parsers with 10kb size limit
app.use(express.json({ limit: '10kb' }));
app.use(express.urlencoded({ extended: true, limit: '10kb' }));

// General Rate Limiter for API endpoints
app.use('/api', generalLimiter);

// Mount API routes
app.use('/api', routes);

// Compatibility routes for Membership & CRM form submissions on Node.js runtime
app.post('/crm/api/memberships/create', async (req, res) => {
  try {
    const body = req.body || {};
    const name = (body.customer_name || body.name || 'VIP Member').trim();
    const phone = (body.phone || '').trim();
    const email = (body.email || '').trim();
    const vehicleModel = (body.vehicle_model || 'Vehicle').trim();
    const vehicleReg = (body.vehicle_reg || '').trim();
    const planName = (body.plan_name || 'Signature Wash Club').trim();
    const billingCycle = (body.billing_cycle || 'Monthly').trim();
    const price = body.price || 2999;
    const memberNo = 'TDC-VIP-' + Math.floor(1000 + Math.random() * 9000);

    await notify({
      type: 'membership_lead',
      ref: memberNo,
      customer: { name, phone, email, vehicle_model: vehicleModel, vehicle_reg: vehicleReg },
      service: `VIP Membership: ${planName} (${billingCycle} - ₹${price})`,
      message: `VIP Club Pass Enrollment: ${planName} (${billingCycle}) for ${vehicleModel} ${vehicleReg ? `[${vehicleReg}]` : ''}`,
      source: 'VIP_MEMBERSHIP',
      date: new Date().toISOString().slice(0, 10),
      time: new Date().toLocaleTimeString('en-GB')
    });

    res.json({
      success: true,
      ok: true,
      member_no: memberNo,
      message: `VIP Member pass ${memberNo} activated for ${name}!`
    });
  } catch (err) {
    res.status(500).json({ success: false, error: err.message });
  }
});

// Compatibility routes for CRM lead status & manual customer directory creation
app.post('/crm/api/leads/status', (req, res) => {
  try {
    const { lead_id, id, ref, stage = 'CONTACTED' } = req.body || {};
    const targetId = lead_id || id || ref;
    const leadsFile = path.resolve(__dirname, '../data/leads.json');
    if (fs.existsSync(leadsFile)) {
      const list = JSON.parse(fs.readFileSync(leadsFile, 'utf8') || '[]');
      let changed = false;
      list.forEach((l) => {
        if (
          (targetId && (String(l.id) === String(targetId) || String(l.ref) === String(targetId))) ||
          (ref && String(l.ref) === String(ref))
        ) {
          l.stage = stage;
          changed = true;
        }
      });
      if (changed) {
        fs.writeFileSync(leadsFile, JSON.stringify(list, null, 2), 'utf8');
      }
    }
    res.json({ success: true, ok: true, message: `Lead stage updated to ${stage}` });
  } catch (err) {
    res.status(500).json({ success: false, error: err.message });
  }
});

app.post('/crm/api/customers/create', (req, res) => {
  try {
    const body = req.body || {};
    const customersFile = path.resolve(__dirname, '../data/customers.json');
    const dataDir = path.dirname(customersFile);
    if (!fs.existsSync(dataDir)) fs.mkdirSync(dataDir, { recursive: true });
    const list = fs.existsSync(customersFile) ? JSON.parse(fs.readFileSync(customersFile, 'utf8') || '[]') : [];
    const newCust = {
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
    list.unshift(newCust);
    fs.writeFileSync(customersFile, JSON.stringify(list, null, 2), 'utf8');
    res.json({ success: true, ok: true, data: newCust, message: 'Customer registered in directory!' });
  } catch (err) {
    res.status(500).json({ success: false, error: err.message });
  }
});

// 404 handler for unmatched API routes
app.all('/api/*', (req, res) => {
  res.status(404).json({
    ok: false,
    error: {
      message: `Endpoint ${req.method} ${req.originalUrl} not found`,
      code: 'NOT_FOUND'
    }
  });
});

// Centralized error handler for API
app.use(errorHandler);

// Static Website & Standalone CRM Server (for Node.js production deployment)
if (fs.existsSync(DIST_DIR)) {
  app.use(express.static(DIST_DIR, { extensions: ['html'] }));

  // CRM direct routes
  app.get('/crm', (req, res) => {
    res.sendFile(path.join(DIST_DIR, 'crm', 'index.html'));
  });

  app.get('/crm/login', (req, res) => {
    res.sendFile(path.join(DIST_DIR, 'crm', 'login.html'));
  });

  app.get('/crm/*', (req, res) => {
    res.sendFile(path.join(DIST_DIR, 'crm', 'index.html'));
  });

  // Clean URL static routing
  app.get('*', (req, res) => {
    const cleanPath = req.path.replace(/^\/+|\/+$/g, '');
    const possibleIndex = path.join(DIST_DIR, cleanPath, 'index.html');
    if (cleanPath && fs.existsSync(possibleIndex)) {
      return res.sendFile(possibleIndex);
    }
    const possibleHtml = path.join(DIST_DIR, `${cleanPath}.html`);
    if (cleanPath && fs.existsSync(possibleHtml)) {
      return res.sendFile(possibleHtml);
    }
    res.sendFile(path.join(DIST_DIR, 'index.html'));
  });
}

export default app;
