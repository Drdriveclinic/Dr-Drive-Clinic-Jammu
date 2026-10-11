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
