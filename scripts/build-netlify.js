/**
 * THE DRIVE CLINIC — Netlify Static & CRM Exporter
 * Generates ready-to-deploy static website & standalone Netlify CRM with client-side persistence
 */

import fs from 'fs';
import path from 'path';
import http from 'http';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const ROOT_DIR = path.resolve(__dirname, '..');
const DIST_DIR = path.join(ROOT_DIR, 'dist');
const WEB_PUBLIC_DIR = path.join(ROOT_DIR, 'web', 'public');

const ROUTES = [
  { url: '/', file: 'index.html' },
  { url: '/services', file: 'services/index.html' },
  { url: '/services/ppf', file: 'services/ppf/index.html' },
  { url: '/services/ceramic-coating', file: 'services/ceramic-coating/index.html' },
  { url: '/services/full-car-detailing', file: 'services/full-car-detailing/index.html' },
  { url: '/services/bike-detailing', file: 'services/bike-detailing/index.html' },
  { url: '/services/interior-deep-clean', file: 'services/interior-deep-clean/index.html' },
  { url: '/services/ceramic-wash', file: 'services/ceramic-wash/index.html' },
  { url: '/services/steam-wash', file: 'services/steam-wash/index.html' },
  { url: '/memberships', file: 'memberships/index.html' },
  { url: '/our-work', file: 'our-work/index.html' },
  { url: '/about', file: 'about/index.html' },
  { url: '/contact', file: 'contact/index.html' },
  { url: '/book', file: 'book/index.html' }
];

function fetchHtml(pathname) {
  return new Promise((resolve, reject) => {
    http.get(`http://127.0.0.1:8000${pathname}`, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => resolve(data));
    }).on('error', err => reject(err));
  });
}

function copyDirRecursive(src, dest) {
  if (!fs.existsSync(dest)) {
    fs.mkdirSync(dest, { recursive: true });
  }
  const entries = fs.readdirSync(src, { withFileTypes: true });
  for (const entry of entries) {
    const srcPath = path.join(src, entry.name);
    const destPath = path.join(dest, entry.name);
    if (entry.isDirectory()) {
      copyDirRecursive(srcPath, destPath);
    } else {
      fs.copyFileSync(srcPath, destPath);
    }
  }
}

async function build() {
  console.log('🚀 Building The Drive Clinic for Netlify...');

  // 1. Ensure clean dist directory
  if (!fs.existsSync(DIST_DIR)) {
    fs.mkdirSync(DIST_DIR, { recursive: true });
  }

  // 2. Copy static assets
  const assetsSrc = path.join(WEB_PUBLIC_DIR, 'assets');
  const assetsDest = path.join(DIST_DIR, 'assets');
  if (fs.existsSync(assetsSrc)) {
    console.log('📦 Copying assets to dist/assets...');
    copyDirRecursive(assetsSrc, assetsDest);
  }

  // Copy robots.txt
  const robotsSrc = path.join(WEB_PUBLIC_DIR, 'robots.txt');
  if (fs.existsSync(robotsSrc)) {
    fs.copyFileSync(robotsSrc, path.join(DIST_DIR, 'robots.txt'));
  }

  // 3. Pre-render public website pages
  console.log('📄 Rendering public website pages...');
  for (const r of ROUTES) {
    const targetFile = path.join(DIST_DIR, r.file);
    const targetDir = path.dirname(targetFile);
    if (!fs.existsSync(targetDir)) {
      fs.mkdirSync(targetDir, { recursive: true });
    }

    try {
      let html = await fetchHtml(r.url);
      
      // Inject Client-side CRM Bridge: Automatically save online leads, bookings & memberships to localStorage
      const crmBridgeScript = `
        <script>
        // Netlify Client-side Lead & Booking Sync to CRM Storage
        (function() {
          function saveToCrmStorage(key, item) {
            try {
              let existing = JSON.parse(localStorage.getItem(key) || '[]');
              existing.unshift(item);
              localStorage.setItem(key, JSON.stringify(existing));
            } catch(e){}
          }

          // Intercept Booking form submissions
          document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.matches('#formPublicEnroll, #modalBookingForm, form[action*="book"], form[action*="contact"], form[action*="leads"]')) {
              const fd = new FormData(form);
              const data = Object.fromEntries(fd.entries());
              data.id = Date.now();
              data.created_at = new Date().toISOString().replace('T', ' ').slice(0, 19);
              data.source = form.id === 'formPublicEnroll' ? 'MEMBERSHIP_PAGE' : 'WEBSITE';
              data.status = 'NEW';
              
              if (form.id === 'formPublicEnroll') {
                saveToCrmStorage('tdc_crm_memberships', {
                  id: Date.now(),
                  member_no: 'TDC-VIP-' + Math.floor(1000 + Math.random() * 9000),
                  customer_name: data.customer_name || 'VIP Member',
                  phone: data.phone || '',
                  email: data.email || '',
                  plan_name: data.plan_name || 'Signature Wash Club',
                  billing_cycle: data.billing_cycle || 'Monthly',
                  price: data.price || '2999',
                  vehicle_model: data.vehicle_model || '',
                  vehicle_reg: data.vehicle_reg || '',
                  remaining_washes: data.billing_cycle === 'Annual' ? 12 : 3,
                  status: 'ACTIVE',
                  created_at: new Date().toISOString().slice(0, 10)
                });
              } else {
                saveToCrmStorage('tdc_crm_leads', {
                  id: Date.now(),
                  customer_name: data.name || data.customer_name || 'Website Inquiry',
                  phone: data.phone || '',
                  email: data.email || '',
                  service_name: data.service_name || data.service || 'PPF & Ceramic Detailing',
                  vehicle_model: data.vehicle || data.vehicle_model || '',
                  vehicle_reg: data.vehicle_reg || '',
                  preferred_date: data.date || '',
                  preferred_time: data.time || '',
                  notes: data.notes || data.message || '',
                  source: 'WEBSITE',
                  stage: 'NEW',
                  created_at: new Date().toISOString().replace('T', ' ').slice(0, 19)
                });
              }
            }
          }, true);
        })();
        </script>
      `;

      html = html.replace('</body>', crmBridgeScript + '\n</body>');
      fs.writeFileSync(targetFile, html, 'utf8');
      console.log(`  ✓ ${r.url} -> dist/${r.file}`);
    } catch (err) {
      console.error(`  ✗ Error rendering ${r.url}:`, err.message);
    }
  }

  // 4. Build Standalone Netlify CRM Application
  console.log('⚡ Generating Standalone Unified CRM for Netlify (/crm)...');
  const crmDir = path.join(DIST_DIR, 'crm');
  if (!fs.existsSync(crmDir)) {
    fs.mkdirSync(crmDir, { recursive: true });
  }

  const crmTemplateSrc = path.join(ROOT_DIR, 'scripts', 'crm-app-template.html');
  if (fs.existsSync(crmTemplateSrc)) {
    const crmHtml = fs.readFileSync(crmTemplateSrc, 'utf8');
    fs.writeFileSync(path.join(crmDir, 'index.html'), crmHtml, 'utf8');
    fs.writeFileSync(path.join(crmDir, 'login.html'), crmHtml, 'utf8');
    console.log('  ✓ Standalone Unified CRM generated at dist/crm/index.html & login.html');
  }

  // Build netlify _redirects
  fs.writeFileSync(path.join(DIST_DIR, '_redirects'), `
/crm/login   /crm/login.html   200
/crm/*       /crm/index.html   200
/*           /index.html       200
  `.trim());

  console.log('✅ Netlify distribution successfully created in /dist!');
}

build();
