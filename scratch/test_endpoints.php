<?php
$urls = [
    '/',
    '/services',
    '/services/ppf',
    '/services/ceramic-coating',
    '/services/full-car-detailing',
    '/services/bike-detailing',
    '/services/interior-deep-clean',
    '/memberships',
    '/our-work',
    '/about',
    '/contact',
    '/book',
    '/crm'
];

foreach ($urls as $u) {
    $ch = curl_init("http://127.0.0.1:8000" . $u);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "[HTTP $code] $u\n";
    curl_close($ch);
}
