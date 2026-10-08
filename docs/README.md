# THE DRIVE CLINIC — Full-Stack Automotive Detailing Platform

> **Client:** THE DRIVE CLINIC  
> **Location:** Nanak Nagar, Jammu, Jammu & Kashmir 180004, India  
> **Tagline:** WHERE CARS GET THEIR SHINE BACK  
> **Tech Stack:** PHP 8.2 (Server-Rendered, SEO-First) + Node.js 20 Express REST API + MySQL 8.0 (InnoDB utf8mb4) + Vanilla ES Modules & Hand-Authored CSS (GSAP 3.12.5 + Lenis 1.1.x)

---

## 1. Project Structure

```
/drive-clinic
  /web            → PHP 8.2 public site (server-rendered, SEO-first)
    /public       → Document root (index.php front controller, assets, .htaccess, robots.txt, sitemap.xml)
    /templates    → Layout shell, 404, and pages (home, services, service-detail, about, contact, book, our-work)
    config.php    → Database & API configuration via environment variables
    db.php        → PDO Singleton database abstraction layer with automatic fallback
  /api            → Node.js 20 + Express REST API (Write operations, slot locking, lead capture)
    /src          → Controllers, services, routes, middleware, validators
    server.js     → API entry point (Port 5000)
  /db             → MySQL 8 schema.sql & seed.sql definitions
  /docs           → README.md, API.md, DEPLOY.md
  start-local.bat → Windows launcher starting both servers in separate terminal windows
```

---

## 2. Quick Local Setup

### Prerequisites:
- **PHP 8.2+** with PDO extension enabled
- **Node.js 20+** and npm
- **MySQL 8.0+** (or use the built-in SQLite/JSON development fallback)

### Step 1: Install API Dependencies
```bash
cd api
npm install
```

### Step 2: Configure Environment Variables
Copy `.env.example` to `.env` in `/api`:
```bash
cp .env.example .env
```
Default configuration:
```ini
PORT=5000
NODE_ENV=development
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=drive_clinic
DB_USER=root
DB_PASS=
CORS_ORIGIN=http://localhost:8000
```

### Step 3: Initialize Database (Optional if using MySQL)
Run the SQL definitions in MySQL 8:
```bash
mysql -u root -p < db/schema.sql
mysql -u root -p drive_clinic < db/seed.sql
```
*(Note: If MySQL is not running locally, the application automatically uses the local SQLite fallback database `web/data_fallback.sqlite` with the seeded 15 services and settings!)*

### Step 4: Launch Local Servers
Run the Windows batch file:
```bash
start-local.bat
```
Or run individually in two separate terminals:
- **Node API Server:**
  ```bash
  cd api && npm start
  # Running at http://127.0.0.1:5000/api
  ```
- **PHP Web Server:**
  ```bash
  cd web/public && php -S 127.0.0.1:8000 index.php
  # Running at http://127.0.0.1:8000
  ```

---

## 3. How to Add a New Service from the Database Alone

The application is **100% data-driven**. To add a new service to the website, insert a row into the `services` table in MySQL. **No code or PHP template modifications are required**:

```sql
INSERT INTO `services` (
  `slug`,
  `name`,
  `category`,
  `vehicle_types`,
  `is_major`,
  `module`,
  `short_desc`,
  `intro_lead`,
  `intro_body`,
  `included_json`,
  `benefits_json`,
  `who_json`,
  `process_json`,
  `hero_slides_json`,
  `related_slugs_json`,
  `duration_note`,
  `sort_order`,
  `is_active`,
  `seo_title`,
  `seo_description`
) VALUES (
  'glass-coating',
  'HYDROPHOBIC GLASS COATING',
  'protect',
  'car',
  1,
  NULL,
  'Ultra-slick hydrophobic rain-repellent coating for windshields and exterior glass.',
  'CRYSTAL CLEAR HIGH-VELOCITY RAIN DEFENSE FOR ENHANCED MONSOON VISIBILITY.',
  'Our fluoropolymer glass coating chemically bonds to windshields, causing rainwater to bead and roll off above 45 km/h without wiper reliance.',
  '["Glass Deep Decontamination", "Cerium Oxide Polishing", "Solvent Surface Prep", "Dual-Layer Hydrophobic Coating", "IR Curing Cycle"]',
  '[{"title":"Rain Repellency","desc":"Water sheets off instantly during heavy Jammu monsoons."},{"title":"Glare Reduction","desc":"Improves night driving clarity and reduces headlight scatter."}]',
  '[{"title":"Highway Drivers","desc":"Essential for high-speed highway driving in rain."}]',
  '[{"step":"01","title":"Glass Cleanse","desc":"Stripping water spots."},{"step":"02","title":"Cerium Polish","desc":"Removing microscopic mineral haze."},{"step":"03","title":"Coating Application","desc":"Even cross-hatch wipe."}]',
  '[{"title":"WINDSHIELD WATER BEADING","caption":"Instant water roll-off on treated glass."}]',
  '["ppf", "ceramic-coating", "ceramic-wash"]',
  '1 - 2 Hours',
  16,
  1,
  'Glass Coating in Jammu | Windshield Rain Repellent | The Drive Clinic',
  'Hydrophobic windshield and glass coating in Nanak Nagar, Jammu. Enhanced monsoon driving visibility.'
);
```

### Result:
- The service will automatically appear in `/services` directory.
- It will automatically render its complete detail page at `/services/glass-coating`.
- It will automatically appear in the `/book` service selection dropdown.
- It will automatically appear in the dynamic `/sitemap.xml`.
