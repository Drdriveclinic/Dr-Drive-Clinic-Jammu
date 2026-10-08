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

        return $pdo;
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
