<?php
// Comprehensive test for Leads Pipeline, Store Inventory Deduction, Payment Settlement, and CSV Export

echo "=== 1. Testing Leads Pipeline Page GET ===\n";
$ch = curl_init('http://127.0.0.1:8000/crm/leads');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "Leads page HTTP Code: " . $code . " (HTML length: " . strlen($html) . " bytes)\n";

// Verify presence of cards from screenshot
if (strpos($html, 'Karan Malhotra') !== false && strpos($html, 'Siddharth Jain') !== false && strpos($html, 'Aman Verma') !== false) {
  echo "✅ Verified: All reference cards (Karan Malhotra, Siddharth Jain, Aman Verma) found in Kanban HTML!\n";
} else {
  echo "⚠️ Warning: Some cards missing from HTML.\n";
}

echo "\n=== 2. Testing Create Lead API ===\n";
$ch = curl_init('http://127.0.0.1:8000/crm/api/leads/create');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
  'customer_name' => 'Rahul Singhania',
  'phone' => '+91 99887 76655',
  'email' => 'rahul.s@test.com',
  'vehicle_model' => 'Porsche Cayenne',
  'vehicle_reg' => 'JK 02 BB 9999',
  'service_name' => 'Full-body PPF',
  'estimated_amount' => '125000',
  'source' => 'Website',
  'stage' => 'NEW',
  'remarks' => 'Requested high-gloss self-healing PPF wrap with 7-year warranty'
]);
$res1 = curl_exec($ch);
curl_close($ch);
echo "Create Lead Response: " . $res1 . "\n";

echo "\n=== 3. Checking Store Inventory BEFORE Quote ===\n";
require_once dirname(__DIR__) . '/web/db.php';
$itemBefore = Database::queryOne("SELECT id, sku, name, current_stock FROM inventory_items WHERE id = 1");
echo "Item before quote: " . $itemBefore['name'] . " (Current Stock: " . $itemBefore['current_stock'] . ")\n";

echo "\n=== 4. Testing Prepare Quote & Auto-Deduct Inventory API ===\n";
// Let's prepare quote for lead 1 with 1x STEK PPF Roll (Item ID 1)
$ch = curl_init('http://127.0.0.1:8000/crm/api/leads/quote');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
  'lead_id' => 1,
  'labor_charge' => 20000,
  'discount' => 0,
  'remarks' => 'Includes 5-Year STEK warranty with first free checkup at 6 months',
  'consumables' => [
    [
      'item_id' => 1,
      'sku' => 'PPF-STEK-15M',
      'name' => 'STEK DYNOshield Gloss PPF Roll',
      'qty' => 1.0,
      'unit_cost' => 48500,
      'sell_price' => 75000
    ]
  ]
]));
$resQuote = curl_exec($ch);
curl_close($ch);
echo "Quote Response: " . $resQuote . "\n";

$itemAfter = Database::queryOne("SELECT id, sku, name, current_stock FROM inventory_items WHERE id = 1");
echo "Item after quote: " . $itemAfter['name'] . " (Current Stock: " . $itemAfter['current_stock'] . ")\n";
if ($itemAfter['current_stock'] < $itemBefore['current_stock']) {
  echo "✅ SUCCESS: Inventory quantity was automatically deducted by 1.0 Roll upon quote generation!\n";
}

echo "\n=== 5. Testing Payment Settlement API ===\n";
$ch = curl_init('http://127.0.0.1:8000/crm/api/leads/payment');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
  'lead_id' => 1,
  'payment_status' => 'PAID',
  'payment_method' => 'UPI',
  'paid_amount' => '112100',
  'stage' => 'COMPLETED'
]);
$resPay = curl_exec($ch);
curl_close($ch);
echo "Payment Settle Response: " . $resPay . "\n";

echo "\n=== 6. Testing CSV Export Endpoint ===\n";
$ch = curl_init('http://127.0.0.1:8000/crm/export/leads');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$csv = curl_exec($ch);
$csvCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "CSV Export HTTP Code: " . $csvCode . " (Size: " . strlen($csv) . " bytes)\n";
echo "First 200 chars of CSV:\n" . substr($csv, 0, 200) . "\n...\n";

echo "\n=== 7. Testing Login Page GET ===\n";
$ch = curl_init('http://127.0.0.1:8000/crm/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$loginHtml = curl_exec($ch);
$loginCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "Login page HTTP Code: " . $loginCode . " (Size: " . strlen($loginHtml) . " bytes)\n";
