<?php
require_once dirname(__DIR__) . '/web/db.php';

$pdo = Database::getConnection();

// Create leads table
$pdo->exec("
CREATE TABLE IF NOT EXISTS leads (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  lead_no TEXT UNIQUE,
  customer_name TEXT NOT NULL,
  phone TEXT NOT NULL,
  email TEXT,
  vehicle_brand TEXT,
  vehicle_model TEXT NOT NULL,
  vehicle_reg TEXT,
  service_id INTEGER,
  service_name TEXT NOT NULL,
  estimated_amount REAL DEFAULT 0,
  source TEXT DEFAULT 'website',
  stage TEXT DEFAULT 'NEW',
  remarks TEXT,
  damages_notes TEXT,
  consumables_json TEXT,
  quote_subtotal REAL DEFAULT 0,
  quote_discount REAL DEFAULT 0,
  quote_gst REAL DEFAULT 0,
  quote_total REAL DEFAULT 0,
  payment_status TEXT DEFAULT 'UNPAID',
  payment_method TEXT,
  paid_amount REAL DEFAULT 0,
  assigned_tech TEXT,
  bay_no INTEGER,
  created_at TEXT,
  updated_at TEXT
);
");

// Create studio users table for login
$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT UNIQUE NOT NULL,
  password_hash TEXT NOT NULL,
  role TEXT DEFAULT 'owner',
  created_at TEXT
);
");

// Insert default studio accounts
$pdo->exec("
INSERT OR REPLACE INTO users (id, name, email, password_hash, role, created_at)
VALUES 
  (1, 'Amit Kapoor', 'owner@thedriveclinic.com', '" . password_hash('clinic2026', PASSWORD_BCRYPT) . "', 'owner', '2026-10-01 10:00:00'),
  (2, 'Rohan Sharma', 'rohan@thedriveclinic.com', '" . password_hash('clinic2026', PASSWORD_BCRYPT) . "', 'lead_tech', '2026-10-01 10:00:00'),
  (3, 'Arjun Mehta', 'arjun@thedriveclinic.com', '" . password_hash('clinic2026', PASSWORD_BCRYPT) . "', 'lead_tech', '2026-10-01 10:00:00');
");

// Clear and seed sample leads matching the user screenshot
$pdo->exec("DELETE FROM leads;");

$leadsData = [
  // 1. New enquiry (3 items)
  [
    'lead_no' => 'LEAD-2026-001',
    'customer_name' => 'Karan Malhotra',
    'phone' => '+91 98123 45670',
    'email' => 'karan.m@gmail.com',
    'vehicle_brand' => 'Audi',
    'vehicle_model' => 'Audi A4',
    'vehicle_reg' => 'JK 02 CD 9090',
    'service_id' => 1,
    'service_name' => 'Full-body PPF',
    'estimated_amount' => 95000,
    'source' => 'Instagram',
    'stage' => 'NEW',
    'remarks' => 'Inquired on Instagram for STEK DynoShield gloss wrap package. Requested slot for Saturday.',
    'created_at' => '2026-10-08 09:15:00'
  ],
  [
    'lead_no' => 'LEAD-2026-002',
    'customer_name' => 'Neha Joshi',
    'phone' => '+91 97234 56781',
    'email' => 'neha.j@gmail.com',
    'vehicle_brand' => 'Tata',
    'vehicle_model' => 'Tata Nexon',
    'vehicle_reg' => 'JK 02 BJ 3344',
    'service_id' => 2,
    'service_name' => 'Ceramic shield',
    'estimated_amount' => 28000,
    'source' => 'Walk-in',
    'stage' => 'NEW',
    'remarks' => 'Visited studio in morning. Wants 3-year 9H ceramic coating with glass treatment.',
    'created_at' => '2026-10-08 10:30:00'
  ],
  [
    'lead_no' => 'LEAD-2026-003',
    'customer_name' => 'Dev Khanna',
    'phone' => '+91 99345 67892',
    'email' => 'dev.k@gmail.com',
    'vehicle_brand' => 'Jeep',
    'vehicle_model' => 'Jeep Compass',
    'vehicle_reg' => 'JK 02 AC 1122',
    'service_id' => 2,
    'service_name' => 'Ceramic shield',
    'estimated_amount' => 28000,
    'source' => 'Walk-in',
    'stage' => 'NEW',
    'remarks' => 'Walked in to check previous customer demo car. Needs paint correction + coating.',
    'created_at' => '2026-10-08 11:45:00'
  ],

  // 2. Contacted (3 items)
  [
    'lead_no' => 'LEAD-2026-004',
    'customer_name' => 'Siddharth Jain',
    'phone' => '+91 98456 78903',
    'email' => 'sid.jain@outlook.com',
    'vehicle_brand' => 'Skoda',
    'vehicle_model' => 'Skoda Slavia',
    'vehicle_reg' => 'JK 02 DG 5566',
    'service_id' => 4,
    'service_name' => 'Signature detailing',
    'estimated_amount' => 8500,
    'source' => 'Referral',
    'stage' => 'CONTACTED',
    'remarks' => 'Referred by Aman Gupta. Contacted via call; agreed on interior deep steam + 2-step machine polish.',
    'created_at' => '2026-10-07 14:20:00'
  ],
  [
    'lead_no' => 'LEAD-2026-005',
    'customer_name' => 'Ritu Sharma',
    'phone' => '+91 98567 89014',
    'email' => 'ritu.sharma@gmail.com',
    'vehicle_brand' => 'MG',
    'vehicle_model' => 'MG Hector',
    'vehicle_reg' => 'JK 02 EF 7788',
    'service_id' => 2,
    'service_name' => 'Ceramic shield',
    'estimated_amount' => 28000,
    'source' => 'Website',
    'stage' => 'CONTACTED',
    'remarks' => 'Submitted website booking request. Discussed Gtechniq Crystal Serum Light package on WhatsApp.',
    'created_at' => '2026-10-07 16:40:00'
  ],
  [
    'lead_no' => 'LEAD-2026-006',
    'customer_name' => 'Meera Iyer',
    'phone' => '+91 98678 90125',
    'email' => 'meera.i@gmail.com',
    'vehicle_brand' => 'Hyundai',
    'vehicle_model' => 'Hyundai i20',
    'vehicle_reg' => 'JK 02 GH 9900',
    'service_id' => 5,
    'service_name' => 'Premium wash',
    'estimated_amount' => 1200,
    'source' => 'Website',
    'stage' => 'CONTACTED',
    'remarks' => 'Booked online wash appointment. Contacted to confirm Friday 11 AM bay slot.',
    'created_at' => '2026-10-07 18:10:00'
  ],

  // 3. Quote sent (2 items)
  [
    'lead_no' => 'LEAD-2026-007',
    'customer_name' => 'Aman Verma',
    'phone' => '+91 98789 01236',
    'email' => 'aman.v@gmail.com',
    'vehicle_brand' => 'Mahindra',
    'vehicle_model' => 'Mahindra XUV700',
    'vehicle_reg' => 'JK 02 JK 1234',
    'service_id' => 1,
    'service_name' => 'Full-body PPF',
    'estimated_amount' => 95000,
    'source' => 'Instagram',
    'stage' => 'QUOTE_SENT',
    'remarks' => 'Sent formal quotation with 1x STEK PPF Roll + ceramic glass booster. Awaiting confirmation deposit.',
    'consumables_json' => json_encode([['item_id' => 1, 'sku' => 'PPF-STEK-15M', 'name' => 'STEK DYNOshield Gloss PPF Roll', 'qty' => 1.0, 'unit_cost' => 48500, 'sell_price' => 75000]]),
    'quote_subtotal' => 80508,
    'quote_gst' => 14492,
    'quote_total' => 95000,
    'payment_status' => 'UNPAID',
    'created_at' => '2026-10-06 11:30:00'
  ],
  [
    'lead_no' => 'LEAD-2026-008',
    'customer_name' => 'Ishita Patel',
    'phone' => '+91 98890 12347',
    'email' => 'ishita.p@gmail.com',
    'vehicle_brand' => 'Volkswagen',
    'vehicle_model' => 'Volkswagen Virtus',
    'vehicle_reg' => 'JK 02 LM 5678',
    'service_id' => 4,
    'service_name' => 'Signature detailing',
    'estimated_amount' => 8500,
    'source' => 'Referral',
    'stage' => 'QUOTE_SENT',
    'remarks' => 'Quotation generated including 2-step compound cut & interior ozone sanitization. Client agreed to review.',
    'consumables_json' => json_encode([['item_id' => 3, 'sku' => 'COMP-KC-H9', 'name' => 'Koch Chemie H9.02 Heavy Cut Compound', 'qty' => 0.2, 'unit_cost' => 2800, 'sell_price' => 1500]]),
    'quote_subtotal' => 7203,
    'quote_gst' => 1297,
    'quote_total' => 8500,
    'payment_status' => 'UNPAID',
    'created_at' => '2026-10-06 15:00:00'
  ],

  // 4. Converted / Completed & Paid
  [
    'lead_no' => 'LEAD-2026-009',
    'customer_name' => 'Sneha Kapoor',
    'phone' => '+91 98901 23458',
    'email' => 'sneha.k@gmail.com',
    'vehicle_brand' => 'Mercedes',
    'vehicle_model' => 'Mercedes GLC',
    'vehicle_reg' => 'MH 01 DJ 9009',
    'service_id' => 1,
    'service_name' => 'Full-body PPF',
    'estimated_amount' => 110000,
    'source' => 'Website',
    'stage' => 'IN_SERVICE',
    'assigned_tech' => 'Rohan Sharma',
    'bay_no' => 1,
    'remarks' => 'Checked in Bay 01. Pre-inspection complete. 1x STEK Roll deducted from store.',
    'consumables_json' => json_encode([['item_id' => 1, 'sku' => 'PPF-STEK-15M', 'name' => 'STEK DYNOshield Gloss PPF Roll', 'qty' => 1.0, 'unit_cost' => 48500, 'sell_price' => 90000]]),
    'quote_subtotal' => 93220,
    'quote_gst' => 16780,
    'quote_total' => 110000,
    'payment_status' => 'PARTIALLY_PAID',
    'payment_method' => 'UPI',
    'paid_amount' => 50000,
    'created_at' => '2026-10-05 10:00:00'
  ],
  [
    'lead_no' => 'LEAD-2026-010',
    'customer_name' => 'Kabir Singh',
    'phone' => '+91 99012 34569',
    'email' => 'kabir.s@gmail.com',
    'vehicle_brand' => 'BMW',
    'vehicle_model' => 'BMW 320d',
    'vehicle_reg' => 'JK 02 AC 4004',
    'service_id' => 2,
    'service_name' => 'Ceramic shield 9H+',
    'estimated_amount' => 35000,
    'source' => 'Walk-in',
    'stage' => 'COMPLETED',
    'assigned_tech' => 'Rohan Sharma',
    'bay_no' => 1,
    'remarks' => '2 coats Gtechniq CSL + EXO v4 applied and cured under IR lamps. 5-year warranty certificate issued.',
    'consumables_json' => json_encode([['item_id' => 2, 'sku' => 'COAT-GT-CSL', 'name' => 'Gtechniq Crystal Serum Light 9H (50ml)', 'qty' => 1.0, 'unit_cost' => 7200, 'sell_price' => 35000]]),
    'quote_subtotal' => 29661,
    'quote_gst' => 5339,
    'quote_total' => 35000,
    'payment_status' => 'PAID',
    'payment_method' => 'POS Card',
    'paid_amount' => 35000,
    'created_at' => '2026-10-04 12:00:00'
  ]
];

$stmt = $pdo->prepare("
  INSERT INTO leads (
    lead_no, customer_name, phone, email, vehicle_brand, vehicle_model, vehicle_reg,
    service_id, service_name, estimated_amount, source, stage, remarks, damages_notes,
    consumables_json, quote_subtotal, quote_discount, quote_gst, quote_total,
    payment_status, payment_method, paid_amount, assigned_tech, bay_no, created_at, updated_at
  ) VALUES (
    :lead_no, :customer_name, :phone, :email, :vehicle_brand, :vehicle_model, :vehicle_reg,
    :service_id, :service_name, :estimated_amount, :source, :stage, :remarks, '',
    :consumables_json, :quote_subtotal, 0, :quote_gst, :quote_total,
    :payment_status, :payment_method, :paid_amount, :assigned_tech, :bay_no, :created_at, :created_at
  )
");

foreach ($leadsData as $d) {
  $stmt->execute([
    ':lead_no' => $d['lead_no'],
    ':customer_name' => $d['customer_name'],
    ':phone' => $d['phone'],
    ':email' => $d['email'] ?? '',
    ':vehicle_brand' => $d['vehicle_brand'] ?? '',
    ':vehicle_model' => $d['vehicle_model'],
    ':vehicle_reg' => $d['vehicle_reg'] ?? '',
    ':service_id' => $d['service_id'] ?? 1,
    ':service_name' => $d['service_name'],
    ':estimated_amount' => $d['estimated_amount'] ?? 0,
    ':source' => $d['source'] ?? 'website',
    ':stage' => $d['stage'] ?? 'NEW',
    ':remarks' => $d['remarks'] ?? '',
    ':consumables_json' => $d['consumables_json'] ?? '[]',
    ':quote_subtotal' => $d['quote_subtotal'] ?? 0,
    ':quote_gst' => $d['quote_gst'] ?? 0,
    ':quote_total' => $d['quote_total'] ?? ($d['estimated_amount'] ?? 0),
    ':payment_status' => $d['payment_status'] ?? 'UNPAID',
    ':payment_method' => $d['payment_method'] ?? '',
    ':paid_amount' => $d['paid_amount'] ?? 0,
    ':assigned_tech' => $d['assigned_tech'] ?? '',
    ':bay_no' => $d['bay_no'] ?? null,
    ':created_at' => $d['created_at']
  ]);
}

echo "Leads migration and seeding completed successfully!\n";
$count = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
echo "Total leads in database: " . $count . "\n";
