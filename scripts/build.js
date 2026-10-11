/**
 * THE DDC — Cloud & Node.js Production Build Script
 * Prepares static distribution, assets, and verified CRM bundles.
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const ROOT_DIR = path.resolve(__dirname, '..');
const DIST_DIR = path.join(ROOT_DIR, 'dist');
const CRM_DIST_DIR = path.join(DIST_DIR, 'crm');
const WEB_PUBLIC_DIR = path.join(ROOT_DIR, 'web', 'public');

function copyDirRecursive(src, dest) {
  if (!fs.existsSync(src)) return;
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

console.log('🚀 Running THE DDC Node.js Production Build...');

// 1. Ensure dist and crm directories exist
[DIST_DIR, CRM_DIST_DIR].forEach(dir => {
  if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
});

// 2. Sync public assets
const assetsSrc = path.join(WEB_PUBLIC_DIR, 'assets');
if (fs.existsSync(assetsSrc)) {
  console.log('📦 Syncing assets to dist/assets...');
  copyDirRecursive(assetsSrc, path.join(DIST_DIR, 'assets'));
  copyDirRecursive(assetsSrc, path.join(CRM_DIST_DIR, 'assets'));
}

// Copy favicon & robots
const robotsSrc = path.join(WEB_PUBLIC_DIR, 'robots.txt');
if (fs.existsSync(robotsSrc)) {
  fs.copyFileSync(robotsSrc, path.join(DIST_DIR, 'robots.txt'));
}

// 3. Ensure CRM HTML template is synced
const crmTemplateSrc = path.join(ROOT_DIR, 'scripts', 'crm-app-template.html');
if (fs.existsSync(crmTemplateSrc)) {
  const crmHtml = fs.readFileSync(crmTemplateSrc, 'utf8');
  fs.writeFileSync(path.join(CRM_DIST_DIR, 'index.html'), crmHtml, 'utf8');
  fs.writeFileSync(path.join(CRM_DIST_DIR, 'login.html'), crmHtml, 'utf8');
  console.log('✓ Synced CRM workspace template to dist/crm/');
}

// 4. Ensure api data directory exists
const apiDataDir = path.join(ROOT_DIR, 'api', 'data');
if (!fs.existsSync(apiDataDir)) {
  fs.mkdirSync(apiDataDir, { recursive: true });
}
['leads.json', 'appointments.json'].forEach(file => {
  const filePath = path.join(apiDataDir, file);
  if (!fs.existsSync(filePath)) {
    fs.writeFileSync(filePath, '[]', 'utf8');
  }
});

console.log('✅ THE DDC Production Build Completed Successfully!');
