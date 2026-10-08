<?php
// Test verification script for all routes, CRM sections, memberships, and APIs

function testUrl($url, $method = 'GET', $data = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            if (is_array($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            }
        }
    }
    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $httpCode, 'body' => $res];
}

$baseUrl = 'http://127.0.0.1:8000';

$pages = [
    'Public Home' => '/',
    'Public Services' => '/services',
    'Public Memberships' => '/memberships',
    'Public About' => '/about',
    'Public Contact' => '/contact',
    'Public Book' => '/book',
    'Public Our Work' => '/our-work',
    'CRM Overview' => '/crm/overview',
    'CRM Leads Pipeline' => '/crm/leads',
    'CRM Job Cards' => '/crm/job-cards',
    'CRM Online Appointments' => '/crm/bookings',
    'CRM Inventory Manager' => '/crm/inventory',
    'CRM Invoices' => '/crm/invoices',
    'CRM Memberships' => '/crm/memberships',
    'CRM Customers & Vehicles' => '/crm/customers',
];

echo "=== TESTING PAGES & CRM SECTIONS ===\n";
foreach ($pages as $label => $path) {
    $res = testUrl($baseUrl . $path);
    $status = ($res['code'] === 200) ? "PASS [200]" : "FAIL [{$res['code']}]";
    echo "{$status} - {$label} ({$path})\n";
}

echo "\n=== TESTING MEMBERSHIP ENROLLMENT API ===\n";
$enrollData = [
    'customer_name' => 'Karanvir Slathia',
    'phone' => '+91 94191 77889',
    'email' => 'karanvir.slathia@gmail.com',
    'vehicle_model' => 'BMW M340i xDrive',
    'vehicle_reg' => 'JK 02 CQ 7700',
    'plan_name' => 'Ceramic Care Pass',
    'billing_cycle' => 'Annual',
    'price' => '14999',
    'washes_total' => '6',
    'perks_notes' => 'Tanzanite Blue II metallic - Gtechniq CSL coated'
];
$enrollRes = testUrl($baseUrl . '/crm/api/memberships/create', 'POST', $enrollData);
echo "Enrollment Response [{$enrollRes['code']}]: " . substr($enrollRes['body'], 0, 150) . "\n";

echo "\n=== TESTING MEMBERSHIP REDEEM API ===\n";
$redeemRes = testUrl($baseUrl . '/crm/api/memberships/redeem', 'POST', json_encode(['member_id' => 1]));
echo "Redeem Response [{$redeemRes['code']}]: " . substr($redeemRes['body'], 0, 150) . "\n";

echo "\n=== TESTING MEMBERSHIP RENEW API ===\n";
$renewRes = testUrl($baseUrl . '/crm/api/memberships/renew', 'POST', json_encode(['member_id' => 4]));
echo "Renew Response [{$renewRes['code']}]: " . substr($renewRes['body'], 0, 150) . "\n";

echo "\n=== TESTING CSV EXPORTS ===\n";
$csvMembers = testUrl($baseUrl . '/crm/export/memberships');
echo "Memberships CSV [{$csvMembers['code']}]: " . strlen($csvMembers['body']) . " bytes received. First line: " . strtok($csvMembers['body'], "\n") . "\n";

$csvLeads = testUrl($baseUrl . '/crm/export/leads');
echo "Leads CSV [{$csvLeads['code']}]: " . strlen($csvLeads['body']) . " bytes received. First line: " . strtok($csvLeads['body'], "\n") . "\n";

echo "\n=== ALL VERIFICATIONS COMPLETE ===\n";
