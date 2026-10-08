<?php
// Test CRM APIs

// 1. Test POST /crm/api/products
$ch = curl_init('http://127.0.0.1:8000/crm/api/products');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
  'sku' => 'TEST-PPF-001',
  'name' => 'STEK Matte Black Stealth PPF (15m Roll)',
  'category' => 'PPF',
  'unit' => 'Rolls',
  'current_stock' => '3.0',
  'min_stock_alert' => '1.0',
  'unit_cost' => '52000',
  'sell_price' => '85000',
  'supplier' => 'STEK India',
  'location' => 'Bay 1 Rack B',
  'notes' => 'Test product creation'
]);
$res1 = curl_exec($ch);
echo "1. Create Product: " . $res1 . "\n";
curl_close($ch);

// 2. Test POST /crm/api/decline
$ch = curl_init('http://127.0.0.1:8000/crm/api/decline');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
  'item_id' => '1',
  'decline_type' => 'DECLINE_SCRAP',
  'quantity' => '0.5',
  'reason' => 'Test cutting scrap during bumper install',
  'logged_by' => 'Rohan Sharma',
  'reference_no' => 'JC-TEST-99'
]);
$res2 = curl_exec($ch);
echo "2. Log Decline: " . $res2 . "\n";
curl_close($ch);

// 3. Test POST /crm/api/stock-in
$ch = curl_init('http://127.0.0.1:8000/crm/api/stock-in');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
  'item_id' => '1',
  'quantity' => '2.0',
  'unit_cost' => '48500',
  'reference_no' => 'PO-RESTOCK-55',
  'logged_by' => 'Amit Kapoor'
]);
$res3 = curl_exec($ch);
echo "3. Stock In: " . $res3 . "\n";
curl_close($ch);
