<?php
require_once dirname(__DIR__) . '/web/db.php';
$pdo = Database::getConnection();

$pdo->exec("DROP TABLE IF EXISTS memberships;");

// Create memberships table
$pdo->exec("
CREATE TABLE memberships (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  member_no TEXT UNIQUE,
  customer_name TEXT NOT NULL,
  phone TEXT NOT NULL,
  email TEXT,
  vehicle_model TEXT NOT NULL,
  vehicle_reg TEXT,
  plan_name TEXT NOT NULL,
  billing_cycle TEXT NOT NULL DEFAULT 'Monthly',
  price REAL NOT NULL,
  status TEXT NOT NULL DEFAULT 'ACTIVE',
  start_date TEXT NOT NULL,
  expiry_date TEXT NOT NULL,
  washes_total INTEGER DEFAULT 3,
  washes_remaining INTEGER DEFAULT 3,
  perks_notes TEXT,
  created_at TEXT,
  updated_at TEXT
);
");


$seedMembers = [
  [
    'member_no' => 'TDC-VIP-001',
    'customer_name' => 'Vikramaditya Sharma',
    'phone' => '+91 94191 22334',
    'email' => 'vikram.sharma@gmail.com',
    'vehicle_model' => 'Audi A6 (Mythos Black)',
    'vehicle_reg' => 'JK 02 AB 1234',
    'plan_name' => 'Ceramic Care Pass',
    'billing_cycle' => 'Annual',
    'price' => 14999,
    'status' => 'ACTIVE',
    'start_date' => '2026-04-10',
    'expiry_date' => '2027-04-10',
    'washes_total' => 6,
    'washes_remaining' => 4,
    'perks_notes' => '2x Annual ceramic maintenance top-ups + 6 signature washes + priority bay booking',
    'created_at' => '2026-04-10 10:30:00'
  ],
  [
    'member_no' => 'TDC-VIP-002',
    'customer_name' => 'Ananya Pandita',
    'phone' => '+91 97960 44556',
    'email' => 'ananya.p@gmail.com',
    'vehicle_model' => 'BMW 320d Luxury Line',
    'vehicle_reg' => 'JK 02 AC 4004',
    'plan_name' => 'Signature Wash Club',
    'billing_cycle' => 'Monthly',
    'price' => 2999,
    'status' => 'ACTIVE',
    'start_date' => '2026-10-01',
    'expiry_date' => '2026-11-01',
    'washes_total' => 3,
    'washes_remaining' => 3,
    'perks_notes' => '3x Signature foam washes + interior steam decontamination + tire gloss treatment',
    'created_at' => '2026-10-01 11:00:00'
  ],
  [
    'member_no' => 'TDC-VIP-003',
    'customer_name' => 'Rahul Jamwal',
    'phone' => '+91 94192 77889',
    'email' => 'rahul.jamwal@yahoo.com',
    'vehicle_model' => 'Toyota Fortuner 4x4',
    'vehicle_reg' => 'JK 02 BB 1020',
    'plan_name' => 'Elite Concierge Detail Pass',
    'billing_cycle' => 'Annual',
    'price' => 29999,
    'status' => 'ACTIVE',
    'start_date' => '2026-01-15',
    'expiry_date' => '2027-01-15',
    'washes_total' => 12,
    'washes_remaining' => 8,
    'perks_notes' => 'Unlimited maintenance foam washes + 15% discount on STEK PPF + studio valet pickup & drop in Jammu',
    'created_at' => '2026-01-15 14:20:00'
  ],
  [
    'member_no' => 'TDC-VIP-004',
    'customer_name' => 'Gurpreet Singh',
    'phone' => '+91 98580 11223',
    'email' => 'gurpreet.s@gmail.com',
    'vehicle_model' => 'Mahindra Thar Roxx',
    'vehicle_reg' => 'JK 02 XY 7788',
    'plan_name' => 'Signature Wash Club',
    'billing_cycle' => 'Monthly',
    'price' => 2999,
    'status' => 'ACTIVE',
    'start_date' => '2026-09-20',
    'expiry_date' => '2026-10-20',
    'washes_total' => 3,
    'washes_remaining' => 1,
    'perks_notes' => 'Underbody mud blasting + 3x premium wash passes',
    'created_at' => '2026-09-20 16:00:00'
  ],
  [
    'member_no' => 'TDC-VIP-005',
    'customer_name' => 'Sunil Bakshi',
    'phone' => '+91 94191 88990',
    'email' => 'sunil.bakshi@rediffmail.com',
    'vehicle_model' => 'Hyundai Creta Knight Edition',
    'vehicle_reg' => 'JK 02 CD 8821',
    'plan_name' => 'Signature Wash Club',
    'billing_cycle' => 'Monthly',
    'price' => 2999,
    'status' => 'EXPIRED',
    'start_date' => '2026-09-01',
    'expiry_date' => '2026-10-01',
    'washes_total' => 3,
    'washes_remaining' => 0,
    'perks_notes' => 'Subscription expired on 1 Oct. Renewal reminder sent via WhatsApp.',
    'created_at' => '2026-09-01 09:45:00'
  ]
];

$stmt = $pdo->prepare("
  INSERT INTO memberships (
    member_no, customer_name, phone, email, vehicle_model, vehicle_reg,
    plan_name, billing_cycle, price, status, start_date, expiry_date,
    washes_total, washes_remaining, perks_notes, created_at
  ) VALUES (
    :member_no, :customer_name, :phone, :email, :vehicle_model, :vehicle_reg,
    :plan_name, :billing_cycle, :price, :status, :start_date, :expiry_date,
    :washes_total, :washes_remaining, :perks_notes, :created_at
  )
");

foreach ($seedMembers as $m) {
  $stmt->execute($m);
}

echo "Memberships migration completed! Total members: " . count($seedMembers) . "\n";
