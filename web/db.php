<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Database PDO Connection & Read Helper
// Provides prepared statement execution with read-only PDO connection.
// Automatically falls back to local SQLite seed if MySQL server is offline.

class Database {
    private static ?PDO $pdo = null;
    private static bool $isFallback = false;

    public static function getConnection(): PDO {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $config = require __DIR__ . '/config.php';
        $dbConfig = $config['db'];

        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $dbConfig['host'],
                $dbConfig['port'],
                $dbConfig['database'],
                $dbConfig['charset']
            );

            self::$pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            self::$isFallback = false;
        } catch (PDOException $e) {
            // If MySQL is not reachable, initialize SQLite local fallback database for seamless rendering
            self::$pdo = self::initSqliteFallback();
            self::$isFallback = true;
        }

        return self::$pdo;
    }

    public static function isFallback(): bool {
        return self::$isFallback;
    }

    public static function query(string $sql, array $params = []): array {
        $db = self::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function queryOne(string $sql, array $params = []): ?array {
        $db = self::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    public static function getSettings(): array {
        $config = require __DIR__ . '/config.php';
        $defaults = $config['studio'];
        
        try {
            $rows = self::query('SELECT setting_key, setting_value FROM settings');
            foreach ($rows as $row) {
                $defaults[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            // Keep defaults
        }

        return $defaults;
    }

    private static function initSqliteFallback(): PDO {
        $sqlitePath = __DIR__ . '/data_fallback.sqlite';
        $needsSeeding = !file_exists($sqlitePath) || filesize($sqlitePath) < 1000;
        
        $pdo = new PDO('sqlite:' . $sqlitePath, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        if ($needsSeeding) {
            self::seedSqlite($pdo);
        }

        self::ensureExtendedSchema($pdo);

        return $pdo;
    }

    private static function ensureExtendedSchema(PDO $pdo): void {
        // Extended CRM Tables
        $pdo->exec('CREATE TABLE IF NOT EXISTS expenses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            expense_no TEXT UNIQUE,
            category TEXT NOT NULL,
            title TEXT NOT NULL,
            amount REAL NOT NULL,
            paid_to TEXT,
            payment_method TEXT DEFAULT "UPI",
            receipt_no TEXT,
            date TEXT NOT NULL,
            notes TEXT,
            created_at TEXT NOT NULL
        );');

        $pdo->exec('CREATE TABLE IF NOT EXISTS staff (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            role TEXT NOT NULL,
            phone TEXT,
            email TEXT,
            base_salary REAL DEFAULT 0,
            incentive_per_job REAL DEFAULT 0,
            status TEXT DEFAULT "ACTIVE",
            join_date TEXT,
            created_at TEXT NOT NULL
        );');

        $pdo->exec('CREATE TABLE IF NOT EXISTS salary_payouts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            payout_no TEXT UNIQUE,
            staff_id INTEGER,
            staff_name TEXT NOT NULL,
            month_name TEXT NOT NULL,
            base_salary REAL NOT NULL,
            incentive_amount REAL DEFAULT 0,
            bonus REAL DEFAULT 0,
            deductions REAL DEFAULT 0,
            net_paid REAL NOT NULL,
            payment_method TEXT DEFAULT "UPI",
            payment_date TEXT NOT NULL,
            status TEXT DEFAULT "PAID",
            notes TEXT,
            created_at TEXT NOT NULL
        );');

        $pdo->exec('CREATE TABLE IF NOT EXISTS whatsapp_campaigns (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            campaign_no TEXT UNIQUE,
            name TEXT NOT NULL,
            audience_group TEXT NOT NULL,
            message_template TEXT NOT NULL,
            status TEXT DEFAULT "COMPLETED",
            recipient_count INTEGER DEFAULT 0,
            created_at TEXT NOT NULL
        );');

        // Safe column additions via ALTER TABLE
        $invoiceCols = array_column($pdo->query('PRAGMA table_info(invoices)')->fetchAll(), 'name');
        if (!in_array('items_json', $invoiceCols)) @$pdo->exec('ALTER TABLE invoices ADD COLUMN items_json TEXT;');
        if (!in_array('customer_name', $invoiceCols)) @$pdo->exec('ALTER TABLE invoices ADD COLUMN customer_name TEXT;');
        if (!in_array('customer_phone', $invoiceCols)) @$pdo->exec('ALTER TABLE invoices ADD COLUMN customer_phone TEXT;');
        if (!in_array('customer_email', $invoiceCols)) @$pdo->exec('ALTER TABLE invoices ADD COLUMN customer_email TEXT;');
        if (!in_array('vehicle_model', $invoiceCols)) @$pdo->exec('ALTER TABLE invoices ADD COLUMN vehicle_model TEXT;');
        if (!in_array('vehicle_reg', $invoiceCols)) @$pdo->exec('ALTER TABLE invoices ADD COLUMN vehicle_reg TEXT;');
        if (!in_array('lead_id', $invoiceCols)) @$pdo->exec('ALTER TABLE invoices ADD COLUMN lead_id INTEGER;');

        $jobCardCols = array_column($pdo->query('PRAGMA table_info(job_cards)')->fetchAll(), 'name');
        if (!in_array('items_json', $jobCardCols)) @$pdo->exec('ALTER TABLE job_cards ADD COLUMN items_json TEXT;');

        $warrantyCols = array_column($pdo->query('PRAGMA table_info(warranty_certificates)')->fetchAll(), 'name');
        if (!in_array('customer_name', $warrantyCols)) @$pdo->exec('ALTER TABLE warranty_certificates ADD COLUMN customer_name TEXT;');
        if (!in_array('customer_phone', $warrantyCols)) @$pdo->exec('ALTER TABLE warranty_certificates ADD COLUMN customer_phone TEXT;');
        if (!in_array('vehicle_model', $warrantyCols)) @$pdo->exec('ALTER TABLE warranty_certificates ADD COLUMN vehicle_model TEXT;');
        if (!in_array('vehicle_reg', $warrantyCols)) @$pdo->exec('ALTER TABLE warranty_certificates ADD COLUMN vehicle_reg TEXT;');
        if (!in_array('coverage_scope', $warrantyCols)) @$pdo->exec('ALTER TABLE warranty_certificates ADD COLUMN coverage_scope TEXT;');
        if (!in_array('notes', $warrantyCols)) @$pdo->exec('ALTER TABLE warranty_certificates ADD COLUMN notes TEXT;');

        // Seed initial staff if empty
        $staffCount = (int)$pdo->query('SELECT COUNT(*) FROM staff')->fetchColumn();
        if ($staffCount === 0) {
            $now = date('Y-m-d H:i:s');
            $staffList = [
                ['Aman Verma', 'Master PPF Applicator', '+91 94191 88201', 'aman.v@thedriveclinic.com', 38000, 1500, 'ACTIVE', '2023-01-15'],
                ['Rajesh Dogra', 'Senior Paint Correction Specialist', '+91 94192 11942', 'rajesh.d@thedriveclinic.com', 32000, 1000, 'ACTIVE', '2023-04-10'],
                ['Simranjeet Singh', 'Ceramic & Graphene Specialist', '+91 98765 44321', 'simran@thedriveclinic.com', 30000, 1200, 'ACTIVE', '2023-07-01'],
                ['Vikrant Sharma', 'Decontamination & Steam Lead', '+91 94193 55102', 'vikrant@thedriveclinic.com', 22000, 500, 'ACTIVE', '2024-02-01']
            ];
            $st = $pdo->prepare('INSERT INTO staff (name, role, phone, email, base_salary, incentive_per_job, status, join_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            foreach ($staffList as $s) {
                $st->execute([...$s, $now]);
            }
        }

        // Seed initial expenses if empty
        $expCount = (int)$pdo->query('SELECT COUNT(*) FROM expenses')->fetchColumn();
        if ($expCount === 0) {
            $now = date('Y-m-d H:i:s');
            $sampleExpenses = [
                ['EXP-2026-001', 'Workshop Rent', 'Studio Bay Lease — Nanak Nagar (October 2026)', 65000, 'Commercial Properties Jammu', 'Bank Transfer', 'LEASE-OCT-26', date('Y-m-02'), 'Studio premises lease', $now],
                ['EXP-2026-002', 'PPF & Film Rolls', 'STEK DYNOshield Gloss Master Roll (15m x 1.52m)', 82000, 'STEK India Official Dist.', 'Bank Transfer', 'INV-STEK-8812', date('Y-m-05'), 'Optically clear TPU self-healing film', $now],
                ['EXP-2026-003', 'Chemicals & Consumables', 'Koch-Chemie Heavy Cut H9.02 & M3.02 Pastes + S0.02 Sealant', 14500, 'AutoCare Imports Delhi', 'UPI', 'UPI-98214431', date('Y-m-08'), 'German compounding chemicals restock', $now],
                ['EXP-2026-004', 'Electricity & Power', 'Jammu Power Distribution Corp (Studio 3-Phase + Silent Gen)', 18400, 'JPDCL Jammu', 'UPI', 'ELEC-JK-8491', date('Y-m-12'), '3-Phase industrial bay power & cleanroom HVAC', $now],
                ['EXP-2026-005', 'Microfibers & Pads', 'Liquid Elements 800 GSM Edgeless Towels & Lake Country Foam Pads', 9200, 'Detailers Hub India', 'Card', 'POS-DH-771', date('Y-m-14'), 'Paint-safe buffing microfiber towels', $now],
                ['EXP-2026-006', 'Staff Welfare & Meals', 'Detailing Bay Team Refreshments & Studio Tea/Lunch', 4600, 'Local Vendors', 'Cash', 'VOUCHER-08', date('Y-m-16'), 'Workshop crew pantry', $now]
            ];
            $et = $pdo->prepare('INSERT INTO expenses (expense_no, category, title, amount, paid_to, payment_method, receipt_no, date, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            foreach ($sampleExpenses as $e) {
                $et->execute($e);
            }
        }

        // Seed sample salary payouts if empty
        $salCount = (int)$pdo->query('SELECT COUNT(*) FROM salary_payouts')->fetchColumn();
        if ($salCount === 0) {
            $now = date('Y-m-d H:i:s');
            $samplePayouts = [
                ['PAY-2026-01', 1, 'Aman Verma', 'September 2026', 38000, 7500, 2000, 0, 47500, 'Bank Wire', '2026-10-01', 'PAID', 'PPF Lead — 5 Full wraps completed', $now],
                ['PAY-2026-02', 2, 'Rajesh Dogra', 'September 2026', 32000, 5000, 1000, 0, 38000, 'UPI', '2026-10-01', 'PAID', 'Paint Correction Lead — 5 Ceramic jobs', $now],
                ['PAY-2026-03', 3, 'Simranjeet Singh', 'September 2026', 30000, 4800, 1000, 0, 35800, 'UPI', '2026-10-01', 'PAID', 'Coating Specialist — 4 Full coatings', $now],
                ['PAY-2026-04', 4, 'Vikrant Sharma', 'September 2026', 22000, 2500, 500, 0, 25000, 'Cash', '2026-10-01', 'PAID', 'Steam & Decon technician', $now]
            ];
            $spt = $pdo->prepare('INSERT INTO salary_payouts (payout_no, staff_id, staff_name, month_name, base_salary, incentive_amount, bonus, deductions, net_paid, payment_method, payment_date, status, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            foreach ($samplePayouts as $sp) {
                $spt->execute($sp);
            }
        }

        // Seed sample PPF warranty certificates if empty
        $warCount = (int)$pdo->query('SELECT COUNT(*) FROM warranty_certificates')->fetchColumn();
        if ($warCount === 0) {
            $now = date('Y-m-d H:i:s');
            $sampleWarranties = [
                ['TDC-WAR-2026-001', 1, 1, 'ppf', 'STEK DYNOshield TPU', 'STK-2026-B849', 7, '2026-08-15', '2033-08-15', '2027-02-15', 'ACTIVE', $now, 'Vikram Sharma', '+91 94191 22334', 'BMW 320d M-Sport', 'JK 02 CQ 7788', 'Full Body Wrap + Headlights', 'Complete edge-wrapped gloss TPU installation'],
                ['TDC-WAR-2026-002', 2, 2, 'ppf', 'Garware Premium Plus', 'GAR-PP-4921', 5, '2026-09-02', '2031-09-02', '2027-03-02', 'ACTIVE', $now, 'Gurpreet Singh', '+91 94192 77881', 'Mahindra Thar Roxx', 'JK 02 DB 0001', 'High Impact Package (Front Bumper, Bonnet, Fenders, ORVMs)', 'Frontal highway stone chip defense'],
                ['TDC-WAR-2026-003', 3, 3, 'ceramic-coating', 'Gtechniq Crystal Serum Ultra 9H', 'GTECH-CSU-902', 5, '2026-09-18', '2031-09-18', '2027-03-18', 'ACTIVE', $now, 'Rohit Jamwal', '+91 94191 55667', 'Mercedes-Benz E220d', 'JK 02 BW 9999', 'Full Body Paint + Alloy Wheels + Glass', 'Molecular composite ceramic quartz matrix']
            ];
            $wt = $pdo->prepare('INSERT INTO warranty_certificates (cert_no, customer_id, vehicle_id, service_slug, product_brand, roll_batch_no, warranty_years, install_date, expiry_date, next_inspection_date, status, created_at, customer_name, customer_phone, vehicle_model, vehicle_reg, coverage_scope, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            foreach ($sampleWarranties as $w) {
                $wt->execute($w);
            }
        }

        // Seed initial sample invoice if empty
        $invCount = (int)$pdo->query('SELECT COUNT(*) FROM invoices')->fetchColumn();
        if ($invCount === 0) {
            $now = date('Y-m-d H:i:s');
            $sampleItems = json_encode([
                ['name' => 'STEK DYNOshield Full Body PPF Installation', 'type' => 'service', 'qty' => 1, 'rate' => 75000, 'total' => 75000],
                ['name' => 'Gtechniq G1 ClearVision Smart Glass Treatment', 'type' => 'product', 'qty' => 1, 'rate' => 3500, 'total' => 3500]
            ]);
            $sub = 78500;
            $gst = round($sub * 0.18, 2);
            $total = $sub + $gst;
            $it = $pdo->prepare('INSERT INTO invoices (invoice_no, customer_id, vehicle_id, subtotal, discount, gst_amount, grand_total, payment_status, payment_method, paid_amount, notes, created_at, items_json, customer_name, customer_phone, customer_email, vehicle_model, vehicle_reg) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $it->execute([
                'INV-2026-001', 1, 1, $sub, 0, $gst, $total, 'PAID', 'UPI', $total,
                'Full body PPF & Smart Glass coating delivered in Bay 01', $now,
                $sampleItems, 'Vikram Sharma', '+91 94191 22334', 'vikram.sharma@example.com', 'BMW 320d M-Sport', 'JK 02 CQ 7788'
            ]);
        }
    }

    private static function seedSqlite(PDO $pdo): void {
        $pdo->exec('CREATE TABLE IF NOT EXISTS settings (id INTEGER PRIMARY KEY AUTOINCREMENT, setting_key TEXT UNIQUE, setting_value TEXT);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS hero_slides (id INTEGER PRIMARY KEY, label TEXT, headline_lines_json TEXT, supporting TEXT, cta_label TEXT, cta_href TEXT, secondary_cta_label TEXT, secondary_cta_href TEXT, image_desktop TEXT, image_mobile TEXT, video_src TEXT, sort_order INTEGER, is_active INTEGER);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS services (id INTEGER PRIMARY KEY, slug TEXT UNIQUE, name TEXT, category TEXT, vehicle_types TEXT, is_major INTEGER, module TEXT, short_desc TEXT, intro_lead TEXT, intro_body TEXT, included_json TEXT, benefits_json TEXT, who_json TEXT, process_json TEXT, hero_slides_json TEXT, related_slugs_json TEXT, duration_note TEXT, sort_order INTEGER, is_active INTEGER, seo_title TEXT, seo_description TEXT);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS faqs (id INTEGER PRIMARY KEY, service_id INTEGER, question TEXT, answer TEXT, is_answered INTEGER, sort_order INTEGER);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS gallery_items (id INTEGER PRIMARY KEY, service_id INTEGER, type TEXT, image TEXT, before_image TEXT, after_image TEXT, alt TEXT, vehicle_label TEXT, sort_order INTEGER);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS reviews (id INTEGER PRIMARY KEY, customer_name TEXT, vehicle TEXT, service_id INTEGER, rating INTEGER, body TEXT, source TEXT, is_published INTEGER);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS availability_rules (id INTEGER PRIMARY KEY, weekday INTEGER, open_time TEXT, close_time TEXT, slot_minutes INTEGER, capacity_per_slot INTEGER, is_active INTEGER);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS customers (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, phone TEXT UNIQUE, whatsapp TEXT, email TEXT, created_at TEXT);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS vehicles (id INTEGER PRIMARY KEY AUTOINCREMENT, customer_id INTEGER, type TEXT, brand TEXT, model TEXT, year TEXT, registration TEXT, color TEXT, created_at TEXT);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS appointments (id INTEGER PRIMARY KEY AUTOINCREMENT, ref TEXT UNIQUE, customer_id INTEGER, vehicle_id INTEGER, service_id INTEGER, date TEXT, time TEXT, status TEXT, is_recurring INTEGER, recurring_weeks INTEGER, notes TEXT, source TEXT, created_at TEXT);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS inventory_items (id INTEGER PRIMARY KEY AUTOINCREMENT, sku TEXT UNIQUE, name TEXT, category TEXT, unit TEXT, current_stock REAL, min_stock_alert REAL, unit_cost REAL, sell_price REAL, supplier TEXT, location TEXT, notes TEXT, updated_at TEXT);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS inventory_transactions (id INTEGER PRIMARY KEY AUTOINCREMENT, item_id INTEGER, type TEXT, quantity REAL, unit_cost REAL, total_cost REAL, reference_no TEXT, reason TEXT, logged_by TEXT, created_at TEXT);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS job_cards (id INTEGER PRIMARY KEY AUTOINCREMENT, card_no TEXT UNIQUE, appointment_id INTEGER, customer_id INTEGER, vehicle_id INTEGER, bay_no INTEGER, assigned_tech TEXT, status TEXT, fuel_level TEXT, odometer TEXT, service_names TEXT, damages_json TEXT, consumables_json TEXT, technician_notes TEXT, total_amount REAL, created_at TEXT, completed_at TEXT);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS invoices (id INTEGER PRIMARY KEY AUTOINCREMENT, invoice_no TEXT UNIQUE, job_card_id INTEGER, customer_id INTEGER, vehicle_id INTEGER, subtotal REAL, discount REAL, gst_amount REAL, grand_total REAL, payment_status TEXT, payment_method TEXT, paid_amount REAL, notes TEXT, created_at TEXT);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS warranty_certificates (id INTEGER PRIMARY KEY AUTOINCREMENT, cert_no TEXT UNIQUE, customer_id INTEGER, vehicle_id INTEGER, service_slug TEXT, product_brand TEXT, roll_batch_no TEXT, warranty_years INTEGER, install_date TEXT, expiry_date TEXT, next_inspection_date TEXT, status TEXT, created_at TEXT);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS leads (id INTEGER PRIMARY KEY AUTOINCREMENT, lead_no TEXT UNIQUE, customer_name TEXT NOT NULL, phone TEXT NOT NULL, email TEXT, vehicle_brand TEXT, vehicle_model TEXT NOT NULL, vehicle_reg TEXT, service_id INTEGER, service_name TEXT NOT NULL, estimated_amount REAL DEFAULT 0, source TEXT DEFAULT "website", stage TEXT DEFAULT "NEW", remarks TEXT, damages_notes TEXT, consumables_json TEXT, quote_subtotal REAL DEFAULT 0, quote_discount REAL DEFAULT 0, quote_gst REAL DEFAULT 0, quote_total REAL DEFAULT 0, payment_status TEXT DEFAULT "UNPAID", payment_method TEXT, paid_amount REAL DEFAULT 0, assigned_tech TEXT, bay_no INTEGER, created_at TEXT, updated_at TEXT);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, email TEXT UNIQUE NOT NULL, password_hash TEXT NOT NULL, role TEXT DEFAULT "owner", created_at TEXT);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS memberships (id INTEGER PRIMARY KEY AUTOINCREMENT, member_no TEXT UNIQUE, customer_name TEXT NOT NULL, phone TEXT NOT NULL, email TEXT, vehicle_model TEXT NOT NULL, vehicle_reg TEXT, plan_name TEXT NOT NULL, billing_cycle TEXT NOT NULL DEFAULT "Monthly", price REAL NOT NULL, status TEXT NOT NULL DEFAULT "ACTIVE", start_date TEXT NOT NULL, expiry_date TEXT NOT NULL, washes_total INTEGER DEFAULT 3, washes_remaining INTEGER DEFAULT 3, perks_notes TEXT, created_at TEXT, updated_at TEXT);');

        // Populate seed data
        $seedSql = file_get_contents(dirname(__DIR__) . '/db/seed.sql');
        if ($seedSql) {
            // Clean MySQL specific syntax for SQLite compatibility
            $queries = preg_split('/;\s*(\r?\n|$)/', $seedSql);
            foreach ($queries as $q) {
                $q = trim($q);
                if (empty($q) || str_starts_with($q, '--') || str_starts_with($q, 'USE ')) continue;
                $q = preg_replace('/ON DUPLICATE KEY UPDATE.*$/is', '', $q);
                $q = preg_replace('/^INSERT INTO/i', 'INSERT OR REPLACE INTO', $q);
                try {
                    $pdo->exec($q);
                } catch (Exception $e) {
                    // Ignore minor syntax differences
                }
            }
        }
    }
}
