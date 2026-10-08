<?php
$routes = [
  'http://127.0.0.1:8000/crm',
  'http://127.0.0.1:8000/crm/overview',
  'http://127.0.0.1:8000/crm/inventory',
  'http://127.0.0.1:8000/crm/job-cards',
  'http://127.0.0.1:8000/crm/bookings',
  'http://127.0.0.1:8000/crm/customers',
  'http://127.0.0.1:8000/crm/invoices',
  'http://127.0.0.1:8000/crm/warranty',
  'http://127.0.0.1:8000/crm/leads',
  'http://127.0.0.1:8000/crm/services',
  'http://127.0.0.1:8000/crm/payments',
  'http://127.0.0.1:8000/crm/analytics'
];

foreach ($routes as $url) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err = curl_error($ch);
  curl_close($ch);
  
  if ($httpCode === 200) {
    echo "[OK 200] {$url} (Length: " . strlen($response) . " bytes)\n";
  } else {
    echo "[FAIL {$httpCode}] {$url} - {$err}\n";
  }
}
