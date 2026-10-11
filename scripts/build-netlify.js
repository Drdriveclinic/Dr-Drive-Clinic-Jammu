/**
 * THE DDC — Dual Deployment Exporter (Website & Standalone CRM)
 * Separates Website and CRM into two independent, standalone deployment packages
 * for distinct domains (e.g. theddc.in & crm.theddc.in).
 */

import fs from 'fs';
import path from 'path';
import http from 'http';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const ROOT_DIR = path.resolve(__dirname, '..');
const DIST_DIR = path.join(ROOT_DIR, 'dist');
const WEBSITE_DIST_DIR = path.join(DIST_DIR, 'website');
const CRM_DIST_DIR = path.join(DIST_DIR, 'crm');
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
  { url: '/services/basic-car-wash', file: 'services/basic-car-wash/index.html' },
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
  console.log('🚀 Building THE DDC Separated Deployments (Website & CRM)...');

  // 1. Ensure clean directories
  [DIST_DIR, WEBSITE_DIST_DIR, CRM_DIST_DIR].forEach(dir => {
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
  });

  // 2. Copy static assets to dist/assets and dist/website/assets
  const assetsSrc = path.join(WEB_PUBLIC_DIR, 'assets');
  if (fs.existsSync(assetsSrc)) {
    console.log('📦 Syncing assets to Website and CRM distribution folders...');
    copyDirRecursive(assetsSrc, path.join(DIST_DIR, 'assets'));
    copyDirRecursive(assetsSrc, path.join(WEBSITE_DIST_DIR, 'assets'));
  }

  // Copy robots.txt & favicon
  const robotsSrc = path.join(WEB_PUBLIC_DIR, 'robots.txt');
  if (fs.existsSync(robotsSrc)) {
    fs.copyFileSync(robotsSrc, path.join(DIST_DIR, 'robots.txt'));
    fs.copyFileSync(robotsSrc, path.join(WEBSITE_DIST_DIR, 'robots.txt'));
  }

  // 3. Render Public Website Pages (for Website Domain)
  console.log('📄 Rendering Public Website Pages (Independent Website Domain)...');
  for (const r of ROUTES) {
    const rootTarget = path.join(DIST_DIR, r.file);
    const webTarget = path.join(WEBSITE_DIST_DIR, r.file);

    [path.dirname(rootTarget), path.dirname(webTarget)].forEach(d => {
      if (!fs.existsSync(d)) fs.mkdirSync(d, { recursive: true });
    });

    try {
      let html = await fetchHtml(r.url);
      
      // Client-side Cross-Domain Lead & Booking Bridge
      const crmBridgeScript = `
        <script>
        // THE DDC — Cross-Domain Lead & Booking Sync Bridge
        (function() {
          const CRM_ENDPOINT = window.THE_DRIVE_CLINIC_CONFIG?.apiBaseUrl || 'https://crm.theddc.in/api';

          function saveLead(lead) {
            try {
              let existing = JSON.parse(localStorage.getItem('tdc_crm_leads') || '[]');
              existing.unshift(lead);
              localStorage.setItem('tdc_crm_leads', JSON.stringify(existing));
            } catch(e){}

            // Post to CRM Domain if available
            try {
              fetch(CRM_ENDPOINT + '/leads', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(lead),
                mode: 'cors'
              }).catch(function(){});
            } catch(e){}
          }

          document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.matches('#formPublicEnroll, #modalBookingForm, form[action*="book"], form[action*="contact"], form[action*="leads"]')) {
              const fd = new FormData(form);
              const data = Object.fromEntries(fd.entries());
              data.id = Date.now();
              data.created_at = new Date().toISOString().replace('T', ' ').slice(0, 19);
              data.source = 'WEBSITE_DOMAIN';
              data.stage = 'NEW';
              saveLead({
                id: data.id,
                customer_name: data.name || data.customer_name || 'Website Lead',
                phone: data.phone || '',
                email: data.email || '',
                service_name: data.service_name || data.service || 'Detailing & Protection',
                vehicle_model: data.vehicle || data.vehicle_model || '',
                notes: data.notes || data.message || '',
                source: 'WEBSITE_DOMAIN',
                stage: 'NEW',
                created_at: data.created_at
              });
            }
          }, true);
        })();
        </script>
      `;

      html = html.replace('</body>', crmBridgeScript + '\n</body>');
      fs.writeFileSync(rootTarget, html, 'utf8');
      fs.writeFileSync(webTarget, html, 'utf8');
      console.log(`  ✓ ${r.url} -> dist/website/${r.file}`);
    } catch (err) {
      console.error(`  ✗ Error rendering ${r.url}:`, err.message);
    }
  }

  // Website standalone _redirects
  fs.writeFileSync(path.join(WEBSITE_DIST_DIR, '_redirects'), '/* /index.html 200\n');
  fs.writeFileSync(path.join(WEBSITE_DIST_DIR, 'package.json'), JSON.stringify({
    name: "the-ddc-website",
    version: "2.0.0",
    description: "THE DDC — Public Website Domain Build",
    scripts: {
      "start": "npx serve ."
    }
  }, null, 2));

  // 4. Build Standalone CRM Application (for CRM Subdomain / Domain)
  console.log('⚡ Generating Standalone Unified CRM (Independent CRM Domain)...');
  const crmTemplateSrc = path.join(ROOT_DIR, 'scripts', 'crm-app-template.html');
  const crmServerSrc = path.join(ROOT_DIR, 'scripts', 'crm-standalone-server.js');
  const crmPkgSrc = path.join(ROOT_DIR, 'scripts', 'crm-standalone-package.json');

  if (fs.existsSync(crmTemplateSrc)) {
    const crmHtml = fs.readFileSync(crmTemplateSrc, 'utf8');
    fs.writeFileSync(path.join(CRM_DIST_DIR, 'index.html'), crmHtml, 'utf8');
    fs.writeFileSync(path.join(CRM_DIST_DIR, 'login.html'), crmHtml, 'utf8');
  }

  if (fs.existsSync(crmServerSrc)) {
    fs.copyFileSync(crmServerSrc, path.join(CRM_DIST_DIR, 'server.js'));
  }

  if (fs.existsSync(crmPkgSrc)) {
    fs.copyFileSync(crmPkgSrc, path.join(CRM_DIST_DIR, 'package.json'));
  }

  // Copy essential assets into CRM directory
  const crmAssetsDir = path.join(CRM_DIST_DIR, 'assets');
  if (!fs.existsSync(crmAssetsDir)) fs.mkdirSync(crmAssetsDir, { recursive: true });
  
  if (fs.existsSync(path.join(WEB_PUBLIC_DIR, 'assets', 'css'))) {
    copyDirRecursive(path.join(WEB_PUBLIC_DIR, 'assets', 'css'), path.join(crmAssetsDir, 'css'));
  }
  if (fs.existsSync(path.join(WEB_PUBLIC_DIR, 'assets', 'brand'))) {
    copyDirRecursive(path.join(WEB_PUBLIC_DIR, 'assets', 'brand'), path.join(crmAssetsDir, 'brand'));
  }
  if (fs.existsSync(path.join(WEB_PUBLIC_DIR, 'assets', 'js'))) {
    copyDirRecursive(path.join(WEB_PUBLIC_DIR, 'assets', 'js'), path.join(crmAssetsDir, 'js'));
  }

  // Standalone CRM Netlify _redirects
  fs.writeFileSync(path.join(CRM_DIST_DIR, '_redirects'), `
/login   /login.html   200
/*       /index.html   200
  `.trim());

  // Also maintain root dist _redirects for local unified dev
  fs.writeFileSync(path.join(DIST_DIR, '_redirects'), `
/crm/login   /crm/login.html   200
/crm/*       /crm/index.html   200
/*           /index.html       200
  `.trim());

  console.log('✅ Separation Complete!');
  console.log('   📁 Website Domain Package: dist/website/ (Upload to theddc.in)');
  console.log('   📁 CRM Domain Package:     dist/crm/     (Upload to crm.theddc.in)');
}

build();
