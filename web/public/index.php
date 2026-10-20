<?php
declare(strict_types=1);

// Start PHP session for Studio CRM authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// THE DRIVE CLINIC — Front Controller & Router
// Plain PHP 8.2 server-rendered site

require_once dirname(__DIR__) . '/db.php';
$config = require dirname(__DIR__) . '/config.php';

// Request URI and method
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Serve static assets directly when using PHP built-in server
$filePath = __DIR__ . $requestUri;
if ($requestUri !== '/' && file_exists($filePath) && is_file($filePath)) {
    return false;
}

// Strip any trailing slash except for root
if ($requestUri !== '/' && str_ends_with($requestUri, '/')) {
    $cleanUri = rtrim($requestUri, '/');
    header("Location: {$cleanUri}", true, 301);
    exit;
}

// Fetch Global Settings
$settings = Database::getSettings();

// Helper to render templates
function render(string $template, array $data = []): void {
    global $config, $settings;
    extract($data);
    $viewPath = dirname(__DIR__) . "/templates/{$template}.php";
    if (!file_exists($viewPath)) {
        http_response_code(500);
        echo "Template {$template} not found.";
        exit;
    }
    require dirname(__DIR__) . '/templates/layout.php';
    exit;
}

// Helper to render CRM templates
function renderCrm(string $template, array $data = []): void {
    global $config, $settings;
    extract($data);
    $viewFile = dirname(__DIR__) . "/templates/crm/{$template}.php";
    $pageTitle = $pageTitle ?? 'THE DRIVE CLINIC — Studio CRM';
    require dirname(__DIR__) . '/templates/crm/layout.php';
    exit;
}

// Helper for 404
function notFound(): void {
    http_response_code(404);
    $pageTitle = '404 - Page Not Found | The Drive Clinic';
    $metaDesc = 'The page you are looking for does not exist or has been moved.';
    $activeNav = '';
    render('404', [
        'pageTitle' => $pageTitle,
        'metaDesc'  => $metaDesc,
        'activeNav' => $activeNav
    ]);
}

// Dynamic Sitemap XML
if ($requestUri === '/sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    $siteUrl = rtrim($config['site_url'], '/');
    $services = Database::query("SELECT slug, updated_at FROM services WHERE is_active = 1 ORDER BY sort_order ASC");
    
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    $staticPages = ['', '/services', '/about', '/contact', '/book', '/our-work'];
    foreach ($staticPages as $page) {
        echo "  <url>\n";
        echo "    <loc>{$siteUrl}{$page}</loc>\n";
        echo "    <changefreq>weekly</changefreq>\n";
        echo "    <priority>" . ($page === '' ? '1.0' : '0.8') . "</priority>\n";
        echo "  </url>\n";
    }

    foreach ($services as $svc) {
        $lastmod = date('Y-m-d', strtotime($svc['updated_at'] ?? 'now'));
        echo "  <url>\n";
        echo "    <loc>{$siteUrl}/services/{$svc['slug']}</loc>\n";
        echo "    <lastmod>{$lastmod}</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.9</priority>\n";
        echo "  </url>\n";
    }
    
    echo '</urlset>';
    exit;
}

// Router dispatch
if ($requestUri === '/') {
    $heroSlides = Database::query("SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY sort_order ASC");
    $services = Database::query("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC");
    $galleryItems = Database::query("SELECT * FROM gallery_items ORDER BY sort_order ASC");
    $faqs = Database::query("SELECT * FROM faqs WHERE is_answered = 1 AND service_id IS NULL ORDER BY sort_order ASC");
    
    render('pages/home', [
        'pageTitle'    => 'The Drive Clinic | Premium Car & Bike Detailing, PPF & Wash in Jammu',
        'metaDesc'     => 'The Drive Clinic is Jammu\'s premier studio for Paint Protection Film (PPF), ceramic coating, precision bike detailing, steam interior care, and signature wash services in Nanak Nagar.',
        'activeNav'    => 'home',
        'heroSlides'   => $heroSlides,
        'services'     => $services,
        'galleryItems' => $galleryItems,
        'faqs'         => $faqs
    ]);
}

if ($requestUri === '/services') {
    $services = Database::query("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC");
    render('pages/services-index', [
        'pageTitle' => 'All Detailing & Protection Services | The Drive Clinic Jammu',
        'metaDesc'  => 'Explore our full suite of automotive care services: Paint Protection Film, Ceramic Coating, Bike Detailing, Steam Sanitization, and Signature Washes.',
        'activeNav' => 'services',
        'services'  => $services
    ]);
}

if (preg_match('#^/services/([a-z0-9\-]+)$#', $requestUri, $matches)) {
    $slug = $matches[1];
    $service = Database::queryOne("SELECT * FROM services WHERE slug = ? AND is_active = 1", [$slug]);
    
    if (!$service) {
        notFound();
    }
    
    $serviceFaqs = Database::query("SELECT * FROM faqs WHERE service_id = ? ORDER BY sort_order ASC", [$service['id']]);
    $serviceGallery = Database::query("SELECT * FROM gallery_items WHERE service_id = ? ORDER BY sort_order ASC", [$service['id']]);
    
    // Fetch Next Service
    $nextService = Database::queryOne("SELECT slug, name, short_desc FROM services WHERE sort_order > ? AND is_active = 1 ORDER BY sort_order ASC LIMIT 1", [$service['sort_order']]);
    if (!$nextService) {
        $nextService = Database::queryOne("SELECT slug, name, short_desc FROM services WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 1");
    }

    render('pages/service-detail', [
        'pageTitle'      => ($service['seo_title'] ?: "{$service['name']} in Jammu | The Drive Clinic"),
        'metaDesc'       => ($service['seo_description'] ?: $service['short_desc']),
        'activeNav'      => 'services',
        'service'        => $service,
        'serviceFaqs'    => $serviceFaqs,
        'serviceGallery' => $serviceGallery,
        'nextService'    => $nextService
    ]);
}

if ($requestUri === '/about') {
    render('pages/about', [
        'pageTitle' => 'About Us | The Drive Clinic Studio Jammu',
        'metaDesc'  => 'Learn about The Drive Clinic — our commitment to automotive craftsmanship, advanced detailing techniques, and premium car and bike care in Jammu.',
        'activeNav' => 'about'
    ]);
}

if ($requestUri === '/contact') {
    $services = Database::query("SELECT slug, name FROM services WHERE is_active = 1 ORDER BY sort_order ASC");
    render('pages/contact', [
        'pageTitle' => 'Contact & Visit Studio | The Drive Clinic Nanak Nagar Jammu',
        'metaDesc'  => 'Visit The Drive Clinic in Nanak Nagar, Jammu. Book an appointment, call us, or get driving directions to our detailing studio.',
        'activeNav' => 'contact',
        'services'  => $services
    ]);
}

if ($requestUri === '/book') {
    $services = Database::query("SELECT slug, name, category, vehicle_types, short_desc, duration_note FROM services WHERE is_active = 1 ORDER BY sort_order ASC");
    $preselectedService = $_GET['service'] ?? '';
    $bookingSource = $_GET['source'] ?? 'direct';

    render('pages/book', [
        'pageTitle'          => 'Book Detailing & Wash Appointment | The Drive Clinic Jammu',
        'metaDesc'           => 'Schedule your detailing, PPF, ceramic coating, or signature wash appointment online at The Drive Clinic.',
        'activeNav'          => 'book',
        'services'           => $services,
        'preselectedService' => $preselectedService,
        'bookingSource'      => $bookingSource
    ]);
}

if ($requestUri === '/our-work') {
    $galleryItems = Database::query("SELECT * FROM gallery_items ORDER BY sort_order ASC");
    render('pages/our-work', [
        'pageTitle'    => 'Our Work & Gallery | The Drive Clinic Transformations',
        'metaDesc'     => 'View real automotive detailing transformations, PPF installations, and ceramic coatings completed at The Drive Clinic in Jammu.',
        'activeNav'    => 'our-work',
        'galleryItems' => $galleryItems
    ]);
}

if ($requestUri === '/memberships') {
    render('pages/memberships', [
        'pageTitle' => 'Membership & VIP Care Pass | The Drive Clinic Jammu',
        'metaDesc'  => 'Join The Drive Clinic Club. Unlimited monthly wash passes, annual ceramic coating top-ups, and VIP studio privileges in Jammu.',
        'activeNav' => 'memberships'
    ]);
}

// Client Self-Service Garage Portal
if ($requestUri === '/portal' || $requestUri === '/customer-panel' || $requestUri === '/my-garage') {
    render('pages/customer-portal', [
        'pageTitle' => 'My Studio Garage & Digital Concierge | The Drive Clinic Jammu',
        'metaDesc'  => 'Client self-service garage portal: live detailing bay tracker, GST tax invoice download, and digital PPF warranty certificates.',
        'activeNav' => 'portal'
    ]);
}

// Dedicated Printable & PDF Views
if (preg_match('#^/crm/invoices/([0-9]+)/print$#', $requestUri, $matches)) {
    $invoiceId = (int)$matches[1];
    require dirname(__DIR__) . '/templates/crm/invoice-print.php';
    exit;
}

if (preg_match('#^/crm/warranty/([0-9]+)/print$#', $requestUri, $matches)) {
    $certId = (int)$matches[1];
    require dirname(__DIR__) . '/templates/crm/warranty-print.php';
    exit;
}

// ==========================================
// CRM API ENDPOINTS
// ==========================================
if ($requestUri === '/crm/api/products' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? 'PPF');
    $unit = trim($_POST['unit'] ?? 'Units');
    $currentStock = (float)($_POST['current_stock'] ?? 0);
    $minStock = (float)($_POST['min_stock_alert'] ?? 2);
    $unitCost = (float)($_POST['unit_cost'] ?? 0);
    $sellPrice = (float)($_POST['sell_price'] ?? 0);
    $supplier = trim($_POST['supplier'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (empty($sku) || empty($name)) {
        echo json_encode(['success' => false, 'error' => 'SKU and Product Name are required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO inventory_items (sku, name, category, unit, current_stock, min_stock_alert, unit_cost, sell_price, supplier, location, notes, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $now = date('Y-m-d H:i:s');
        $stmt->execute([$sku, $name, $category, $unit, $currentStock, $minStock, $unitCost, $sellPrice, $supplier, $location, $notes, $now]);
        $itemId = (int)$db->lastInsertId();

        if ($currentStock > 0) {
            $txStmt = $db->prepare("INSERT INTO inventory_transactions (item_id, type, quantity, unit_cost, total_cost, reference_no, reason, logged_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $txStmt->execute([$itemId, 'IN_PURCHASE', $currentStock, $unitCost, ($currentStock * $unitCost), 'INITIAL-STOCK', 'Initial store inventory opening balance', 'Admin', $now]);
        }

        echo json_encode(['success' => true, 'message' => "Product {$name} added to store successfully!"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/stock-in' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $itemId = (int)($_POST['item_id'] ?? 0);
    $quantity = (float)($_POST['quantity'] ?? 0);
    $unitCost = (float)($_POST['unit_cost'] ?? 0);
    $refNo = trim($_POST['reference_no'] ?? '');
    $loggedBy = trim($_POST['logged_by'] ?? 'Studio Administrator');

    if ($itemId <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'error' => 'Valid product and quantity required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $totalCost = $quantity * $unitCost;
        $now = date('Y-m-d H:i:s');

        // Update item stock
        $upStmt = $db->prepare("UPDATE inventory_items SET current_stock = current_stock + ?, unit_cost = ?, updated_at = ? WHERE id = ?");
        $upStmt->execute([$quantity, $unitCost, $now, $itemId]);

        // Record transaction
        $txStmt = $db->prepare("INSERT INTO inventory_transactions (item_id, type, quantity, unit_cost, total_cost, reference_no, reason, logged_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $txStmt->execute([$itemId, 'IN_PURCHASE', $quantity, $unitCost, $totalCost, $refNo, 'Stock Restock / Purchase', $loggedBy, $now]);

        echo json_encode(['success' => true, 'message' => "Stock in of +{$quantity} units recorded successfully!"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/decline' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $itemId = (int)($_POST['item_id'] ?? 0);
    $declineType = trim($_POST['decline_type'] ?? 'DECLINE_DAMAGED');
    $quantity = (float)($_POST['quantity'] ?? 0);
    $reason = trim($_POST['reason'] ?? '');
    $loggedBy = trim($_POST['logged_by'] ?? 'Studio Administrator');
    $refNo = trim($_POST['reference_no'] ?? '');

    if ($itemId <= 0 || $quantity <= 0 || empty($reason)) {
        echo json_encode(['success' => false, 'error' => 'Product, decline quantity, and detailed incident reason are required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $item = Database::queryOne("SELECT * FROM inventory_items WHERE id = ?", [$itemId]);
        if (!$item) {
            echo json_encode(['success' => false, 'error' => 'Product not found']);
            exit;
        }

        $unitCost = (float)$item['unit_cost'];
        $totalLoss = $quantity * $unitCost;
        $now = date('Y-m-d H:i:s');

        // Update item stock: subtract quantity
        $newStock = max(0, (float)$item['current_stock'] - $quantity);
        $upStmt = $db->prepare("UPDATE inventory_items SET current_stock = ?, updated_at = ? WHERE id = ?");
        $upStmt->execute([$newStock, $now, $itemId]);

        // Insert into decline audit ledger
        $txStmt = $db->prepare("INSERT INTO inventory_transactions (item_id, type, quantity, unit_cost, total_cost, reference_no, reason, logged_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $txStmt->execute([$itemId, $declineType, -$quantity, $unitCost, -$totalLoss, $refNo, $reason, $loggedBy, $now]);

        echo json_encode([
            'success' => true,
            'message' => "Decline of {$quantity} {$item['unit']} recorded. Total loss written off: ₹" . number_format($totalLoss, 2),
            'new_stock' => $newStock
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/bookings' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $name = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['customer_phone'] ?? '');
    $vehicleModel = trim($_POST['vehicle_model'] ?? '');
    $vehicleReg = trim($_POST['vehicle_reg'] ?? '');
    $serviceId = (int)($_POST['service_id'] ?? 1);
    $bayNo = (int)($_POST['bay_no'] ?? 1);
    $date = trim($_POST['date'] ?? date('Y-m-d'));
    $time = trim($_POST['time'] ?? '10:00');
    $notes = trim($_POST['notes'] ?? '');

    if (empty($name) || empty($phone) || empty($vehicleModel)) {
        echo json_encode(['success' => false, 'error' => 'Customer name, phone, and vehicle model are required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        // Check or create customer
        $cust = Database::queryOne("SELECT id FROM customers WHERE phone = ?", [$phone]);
        if ($cust) {
            $customerId = (int)$cust['id'];
        } else {
            $stmt = $db->prepare("INSERT INTO customers (name, phone, whatsapp, created_at) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $phone, $phone, date('Y-m-d H:i:s')]);
            $customerId = (int)$db->lastInsertId();
        }

        // Create vehicle
        $vStmt = $db->prepare("INSERT INTO vehicles (customer_id, type, brand, model, registration, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        $vStmt->execute([$customerId, 'Car', 'Vehicle', $vehicleModel, $vehicleReg, date('Y-m-d H:i:s')]);
        $vehicleId = (int)$db->lastInsertId();

        // Create appointment
        $ref = 'TDC-' . strtoupper(substr(md5(uniqid()), 0, 6));
        $aStmt = $db->prepare("INSERT INTO appointments (ref, customer_id, vehicle_id, service_id, date, time, status, notes, source, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $aStmt->execute([$ref, $customerId, $vehicleId, $serviceId, $date, $time, 'CONFIRMED', $notes, 'crm_direct', date('Y-m-d H:i:s')]);

        // Sync to leads pipeline
        $leadStmt = $db->prepare("
            INSERT INTO leads (
                lead_no, customer_name, phone, vehicle_model, vehicle_reg,
                service_id, service_name, estimated_amount, source, stage, remarks,
                quote_total, payment_status, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, 'Studio Detailing', 15000, 'Walk-in', 'IN_SERVICE', ?, 15000, 'UNPAID', ?, ?)
        ");
        $now = date('Y-m-d H:i:s');
        $leadStmt->execute([$ref, $name, $phone, $vehicleModel, $vehicleReg, $serviceId, $notes, $now, $now]);

        echo json_encode(['success' => true, 'message' => "Booking {$ref} created for {$name}!"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/invoices/pay' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $id = (int)($input['id'] ?? 0);
    if ($id > 0) {
        Database::getConnection()->exec("UPDATE invoices SET payment_status = 'PAID', paid_amount = grand_total WHERE id = {$id}");
        echo json_encode(['success' => true, 'message' => 'Invoice marked as paid']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    }
    exit;
}

if ($requestUri === '/crm/api/invoices/create' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $custName = trim($input['customer_name'] ?? '');
    $custPhone = trim($input['customer_phone'] ?? '');
    $custEmail = trim($input['customer_email'] ?? '');
    $vehModel = trim($input['vehicle_model'] ?? 'Car');
    $vehReg = trim($input['vehicle_reg'] ?? '');
    $items = $input['items'] ?? [];
    $discount = (float)($input['discount'] ?? 0);
    $hasGst = !empty($input['has_gst']);
    $paymentStatus = trim($input['payment_status'] ?? 'PENDING');
    $paymentMethod = trim($input['payment_method'] ?? 'UPI');
    $paidAmount = (float)($input['paid_amount'] ?? 0);
    $notes = trim($input['notes'] ?? '');

    if (empty($custName) || empty($custPhone)) {
        echo json_encode(['success' => false, 'error' => 'Customer name and phone are required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $now = date('Y-m-d H:i:s');
        $invNo = 'INV-' . date('Y') . '-' . sprintf('%03d', rand(100, 999));

        // Sync customer
        $cust = Database::queryOne("SELECT id FROM customers WHERE phone = ?", [$custPhone]);
        if ($cust) {
            $custId = (int)$cust['id'];
        } else {
            $cStmt = $db->prepare("INSERT INTO customers (name, phone, whatsapp, email, created_at) VALUES (?, ?, ?, ?, ?)");
            $cStmt->execute([$custName, $custPhone, $custPhone, $custEmail ?: null, $now]);
            $custId = (int)$db->lastInsertId();
        }

        // Sync vehicle
        $vCheck = Database::queryOne("SELECT id FROM vehicles WHERE customer_id = ? AND model = ?", [$custId, $vehModel]);
        if ($vCheck) {
            $vehId = (int)$vCheck['id'];
        } else {
            $vStmt = $db->prepare("INSERT INTO vehicles (customer_id, type, brand, model, registration, created_at) VALUES (?, 'Car', 'Vehicle', ?, ?, ?)");
            $vStmt->execute([$custId, $vehModel, $vehReg, $now]);
            $vehId = (int)$db->lastInsertId();
        }

        // Compute subtotal from items
        $subtotal = 0.0;
        foreach ($items as $it) {
            $qty = (float)($it['qty'] ?? 1);
            $rate = (float)($it['rate'] ?? $it['price'] ?? 0);
            $subtotal += ($qty * $rate);
        }
        $netSubtotal = max(0, $subtotal - $discount);
        $gstAmount = $hasGst ? round($netSubtotal * 0.18, 2) : 0;
        $grandTotal = $netSubtotal + $gstAmount;
        if ($paymentStatus === 'PAID' && $paidAmount <= 0) {
            $paidAmount = $grandTotal;
        }

        $invStmt = $db->prepare("
            INSERT INTO invoices (
                invoice_no, customer_id, vehicle_id, subtotal, discount, gst_amount,
                grand_total, payment_status, payment_method, paid_amount, notes,
                items_json, customer_name, customer_phone, customer_email,
                vehicle_model, vehicle_reg, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $invStmt->execute([
            $invNo, $custId, $vehId, $subtotal, $discount, $gstAmount,
            $grandTotal, $paymentStatus, $paymentMethod, $paidAmount, $notes,
            json_encode($items), $custName, $custPhone, $custEmail ?: null,
            $vehModel, $vehReg, $now
        ]);
        $invoiceId = (int)$db->lastInsertId();

        echo json_encode([
            'success' => true,
            'invoice_id' => $invoiceId,
            'invoice_no' => $invNo,
            'message' => "Invoice {$invNo} generated for {$custName}!"
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/job-cards/create' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $custName = trim($input['customer_name'] ?? '');
    $custPhone = trim($input['customer_phone'] ?? '');
    $vehModel = trim($input['vehicle_model'] ?? 'Car');
    $vehReg = trim($input['vehicle_reg'] ?? '');
    $bayNo = (int)($input['bay_no'] ?? 1);
    $assignedTech = trim($input['assigned_tech'] ?? 'Master Tech');
    $serviceNames = trim($input['service_names'] ?? 'Custom Detailing');
    $itemsJson = is_string($input['items_json'] ?? null) ? $input['items_json'] : json_encode($input['items'] ?? []);
    $totalAmount = (float)($input['total_amount'] ?? 0);
    $fuelLevel = trim($input['fuel_level'] ?? '1/2 Tank');
    $odometer = trim($input['odometer'] ?? '');
    $technicianNotes = trim($input['technician_notes'] ?? '');

    if (empty($custName) || empty($custPhone)) {
        echo json_encode(['success' => false, 'error' => 'Customer name and phone are required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $now = date('Y-m-d H:i:s');
        $cardNo = 'JC-' . date('Y') . '-' . sprintf('%03d', rand(100, 999));

        // Sync customer
        $cust = Database::queryOne("SELECT id FROM customers WHERE phone = ?", [$custPhone]);
        if ($cust) {
            $custId = (int)$cust['id'];
        } else {
            $cStmt = $db->prepare("INSERT INTO customers (name, phone, whatsapp, created_at) VALUES (?, ?, ?, ?)");
            $cStmt->execute([$custName, $custPhone, $custPhone, $now]);
            $custId = (int)$db->lastInsertId();
        }

        // Sync vehicle
        $vCheck = Database::queryOne("SELECT id FROM vehicles WHERE customer_id = ? AND model = ?", [$custId, $vehModel]);
        if ($vCheck) {
            $vehId = (int)$vCheck['id'];
        } else {
            $vStmt = $db->prepare("INSERT INTO vehicles (customer_id, type, brand, model, registration, created_at) VALUES (?, 'Car', 'Vehicle', ?, ?, ?)");
            $vStmt->execute([$custId, $vehModel, $vehReg, $now]);
            $vehId = (int)$db->lastInsertId();
        }

        $jcStmt = $db->prepare("
            INSERT INTO job_cards (
                card_no, customer_id, vehicle_id, bay_no, assigned_tech, status,
                fuel_level, odometer, service_names, items_json, technician_notes,
                total_amount, created_at
            ) VALUES (?, ?, ?, ?, ?, 'IN_PROGRESS', ?, ?, ?, ?, ?, ?, ?)
        ");
        $jcStmt->execute([
            $cardNo, $custId, $vehId, $bayNo, $assignedTech,
            $fuelLevel, $odometer, $serviceNames, $itemsJson, $technicianNotes,
            $totalAmount, $now
        ]);
        $jcId = (int)$db->lastInsertId();

        // Also add lead in service
        $ref = 'LEAD-' . date('Y') . '-' . sprintf('%03d', rand(100, 999));
        $lStmt = $db->prepare("
            INSERT INTO leads (
                lead_no, customer_name, phone, vehicle_model, vehicle_reg,
                service_name, estimated_amount, source, stage, remarks,
                quote_total, payment_status, assigned_tech, bay_no, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'Studio Check-in', 'IN_SERVICE', ?, ?, 'UNPAID', ?, ?, ?, ?)
        ");
        $lStmt->execute([
            $ref, $custName, $custPhone, $vehModel, $vehReg,
            $serviceNames, $totalAmount, $technicianNotes, $totalAmount,
            $assignedTech, $bayNo, $now, $now
        ]);

        echo json_encode([
            'success' => true,
            'job_card_id' => $jcId,
            'card_no' => $cardNo,
            'message' => "Job card {$cardNo} created for {$custName} in Bay 0{$bayNo}!"
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/job-cards/status' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $id = (int)($input['id'] ?? 0);
    $status = trim($input['status'] ?? 'COMPLETED');
    if ($id > 0) {
        $now = date('Y-m-d H:i:s');
        Database::getConnection()->exec("UPDATE job_cards SET status = '{$status}', completed_at = '{$now}' WHERE id = {$id}");
        echo json_encode(['success' => true, 'message' => "Job card marked {$status}!"]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    }
    exit;
}

if ($requestUri === '/crm/api/expenses/create' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $category = trim($_POST['category'] ?? 'Chemicals & Consumables');
    $title = trim($_POST['title'] ?? '');
    $amount = (float)($_POST['amount'] ?? 0);
    $paidTo = trim($_POST['paid_to'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? 'UPI');
    $receiptNo = trim($_POST['receipt_no'] ?? '');
    $date = trim($_POST['date'] ?? date('Y-m-d'));
    $notes = trim($_POST['notes'] ?? '');

    if (empty($title) || $amount <= 0) {
        echo json_encode(['success' => false, 'error' => 'Title and positive amount are required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $now = date('Y-m-d H:i:s');
        $expNo = 'EXP-' . date('Y') . '-' . sprintf('%03d', rand(100, 999));
        $stmt = $db->prepare("INSERT INTO expenses (expense_no, category, title, amount, paid_to, payment_method, receipt_no, date, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$expNo, $category, $title, $amount, $paidTo, $paymentMethod, $receiptNo, $date, $notes, $now]);
        echo json_encode(['success' => true, 'message' => "Expense {$expNo} logged successfully!"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/expenses/delete' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $id = (int)($input['id'] ?? 0);
    if ($id > 0) {
        Database::getConnection()->exec("DELETE FROM expenses WHERE id = {$id}");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    }
    exit;
}

if ($requestUri === '/crm/api/staff/create' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $name = trim($_POST['name'] ?? '');
    $role = trim($_POST['role'] ?? 'Master Detailer');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $baseSalary = (float)($_POST['base_salary'] ?? 0);
    $incentive = (float)($_POST['incentive_per_job'] ?? 1000);
    $joinDate = trim($_POST['join_date'] ?? date('Y-m-d'));
    $status = trim($_POST['status'] ?? 'ACTIVE');

    if (empty($name) || empty($phone)) {
        echo json_encode(['success' => false, 'error' => 'Staff name and phone are required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $now = date('Y-m-d H:i:s');
        $stmt = $db->prepare("INSERT INTO staff (name, role, phone, email, base_salary, incentive_per_job, status, join_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $role, $phone, $email ?: null, $baseSalary, $incentive, $status, $joinDate, $now]);
        echo json_encode(['success' => true, 'message' => "Staff member {$name} registered!"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/salary/create' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $staffId = (int)($_POST['staff_id'] ?? 0);
    $staffName = trim($_POST['staff_name'] ?? '');
    $monthName = trim($_POST['month_name'] ?? date('F Y'));
    $baseSalary = (float)($_POST['base_salary'] ?? 0);
    $incentive = (float)($_POST['incentive_amount'] ?? 0);
    $bonus = (float)($_POST['bonus'] ?? 0);
    $deductions = (float)($_POST['deductions'] ?? 0);
    $netPaid = (float)($_POST['net_paid'] ?? (($baseSalary + $incentive + $bonus) - $deductions));
    $paymentMethod = trim($_POST['payment_method'] ?? 'UPI');
    $paymentDate = trim($_POST['payment_date'] ?? date('Y-m-d'));
    $notes = trim($_POST['notes'] ?? '');

    if (empty($staffName) || $netPaid <= 0) {
        echo json_encode(['success' => false, 'error' => 'Valid staff member and payout amount required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $now = date('Y-m-d H:i:s');
        $payoutNo = 'PAY-' . date('Y') . '-' . sprintf('%03d', rand(100, 999));
        $stmt = $db->prepare("INSERT INTO salary_payouts (payout_no, staff_id, staff_name, month_name, base_salary, incentive_amount, bonus, deductions, net_paid, payment_method, payment_date, status, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PAID', ?, ?)");
        $stmt->execute([$payoutNo, $staffId, $staffName, $monthName, $baseSalary, $incentive, $bonus, $deductions, $netPaid, $paymentMethod, $paymentDate, $notes, $now]);
        echo json_encode(['success' => true, 'message' => "Salary {$payoutNo} recorded for {$staffName}!"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/warranty/create' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $custName = trim($_POST['customer_name'] ?? '');
    $custPhone = trim($_POST['customer_phone'] ?? '');
    $vehModel = trim($_POST['vehicle_model'] ?? 'Car');
    $vehReg = trim($_POST['vehicle_reg'] ?? '');
    $productBrand = trim($_POST['product_brand'] ?? 'STEK DYNOshield TPU');
    $batchNo = trim($_POST['roll_batch_no'] ?? '');
    $years = (int)($_POST['warranty_years'] ?? 5);
    $scope = trim($_POST['coverage_scope'] ?? 'Full Body');
    $installDate = trim($_POST['install_date'] ?? date('Y-m-d'));
    $nextInsp = trim($_POST['next_inspection_date'] ?? date('Y-m-d', strtotime('+6 months')));
    $notes = trim($_POST['notes'] ?? '');

    if (empty($custName) || empty($batchNo)) {
        echo json_encode(['success' => false, 'error' => 'Customer name and roll batch number are required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $now = date('Y-m-d H:i:s');
        $certNo = 'TDC-WAR-' . date('Y') . '-' . sprintf('%03d', rand(100, 999));
        $expiryDate = date('Y-m-d', strtotime("+{$years} years", strtotime($installDate)));

        // Sync customer
        $cust = Database::queryOne("SELECT id FROM customers WHERE phone = ?", [$custPhone]);
        $custId = $cust ? (int)$cust['id'] : null;

        $stmt = $db->prepare("
            INSERT INTO warranty_certificates (
                cert_no, customer_id, service_slug, product_brand, roll_batch_no,
                warranty_years, coverage_scope, install_date, expiry_date,
                next_inspection_date, status, customer_name, customer_phone,
                vehicle_model, vehicle_reg, notes, created_at
            ) VALUES (?, ?, 'ppf', ?, ?, ?, ?, ?, ?, ?, 'ACTIVE', ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $certNo, $custId, $productBrand, $batchNo,
            $years, $scope, $installDate, $expiryDate,
            $nextInsp, $custName, $custPhone,
            $vehModel, $vehReg, $notes, $now
        ]);
        $certId = (int)$db->lastInsertId();

        echo json_encode([
            'success' => true,
            'cert_id' => $certId,
            'cert_no' => $certNo,
            'message' => "Warranty certificate {$certNo} issued for {$custName}!"
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/whatsapp/campaign' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $name = trim($input['name'] ?? 'Seasonal Broadcast');
    $audience = trim($input['audience_group'] ?? 'ALL');
    $msg = trim($input['message_template'] ?? '');

    try {
        $db = Database::getConnection();
        $now = date('Y-m-d H:i:s');
        $campNo = 'CAMP-' . date('Y') . '-' . sprintf('%03d', rand(100, 999));
        $stmt = $db->prepare("INSERT INTO whatsapp_campaigns (campaign_no, name, audience_group, message_template, status, recipient_count, created_at) VALUES (?, ?, ?, ?, 'ACTIVE_QUEUE', 0, ?)");
        $stmt->execute([$campNo, $name, $audience, $msg, $now]);
        echo json_encode(['success' => true, 'campaign_no' => $campNo]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// GoDaddy Business Email Dispatcher (care@thedriveclinic.com)
function dispatchGoDaddyEmail(string $type, array $data): void {
    global $config;
    $recipient = $config['mail']['to'] ?? 'care@thedriveclinic.com';
    $name = $data['name'] ?? $data['customer_name'] ?? 'Website Client';
    $service = $data['service'] ?? $data['service_name'] ?? 'Studio Detailing';
    $ref = $data['ref'] ?? $data['lead_no'] ?? ('DRC-' . rand(1000, 9999));

    $subject = ($type === 'booking')
        ? "⚡ New Studio Booking Confirmed [{$ref}]: {$name} — {$service}"
        : "🔥 New Lead Received [{$ref}]: {$name} — {$service}";

    $cleanPhone = preg_replace('/\D/', '', (string)($data['phone'] ?? ''));
    $waLink = $cleanPhone ? "https://wa.me/" . (str_starts_with($cleanPhone, '91') ? $cleanPhone : "91" . $cleanPhone) : 'N/A';

    $body = "THE DRIVE CLINIC // STUDIO MANAGEMENT // JAMMU\n";
    $body .= "Target Mailbox: care@thedriveclinic.com\n";
    $body .= "====================================================\n";
    $body .= "EVENT: " . strtoupper($type) . "\n";
    $body .= "REFERENCE: {$ref}\n";
    $body .= "CUSTOMER: {$name}\n";
    $body .= "PHONE: " . ($data['phone'] ?? 'N/A') . " (WhatsApp: {$waLink})\n";
    $body .= "EMAIL: " . ($data['email'] ?? 'Not provided') . "\n";
    $body .= "VEHICLE: " . ($data['vehicle'] ?? $data['vehicle_model'] ?? 'Vehicle') . "\n";
    $body .= "SERVICE: {$service}\n";
    if (!empty($data['date'])) $body .= "SCHEDULE: " . $data['date'] . " at " . ($data['time'] ?? '10:00') . "\n";
    if (!empty($data['message']) || !empty($data['notes'])) $body .= "MESSAGE: " . ($data['message'] ?? $data['notes'] ?? '') . "\n";
    $body .= "SOURCE: " . ($data['source'] ?? 'Website') . "\n";
    $body .= "TIMESTAMP: " . date('Y-m-d H:i:s') . " IST\n";
    $body .= "====================================================\n";
    $body .= "Open CRM: " . ($config['site_url'] ?? 'http://localhost:8000') . "/crm\n";

    $headers = "From: " . ($config['mail']['from'] ?? 'care@thedriveclinic.com') . "\r\n" .
               "Reply-To: " . ($data['email'] ?? 'care@thedriveclinic.com') . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    @mail($recipient, $subject, $body, $headers);

    // Audit log
    $logDir = dirname(__DIR__, 2) . '/api/logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
    @file_put_contents($logDir . '/godaddy_mail_outbox.log', "[" . date('c') . "] [PHP] TYPE: {$type} | TO: {$recipient} | SUBJECT: {$subject}\n" . json_encode($data, JSON_PRETTY_PRINT) . "\n------------------------------------------------------------\n", FILE_APPEND);
}

// ==========================================
// UNIFIED PUBLIC API (LEADS & APPOINTMENTS)
// ==========================================
if ($requestUri === '/api/leads' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $name = trim($input['name'] ?? $input['customer_name'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $email = trim($input['email'] ?? '');
    $vehicleType = trim($input['vehicle_type'] ?? 'car');
    $vehicleModel = trim($input['vehicle_model'] ?? ($vehicleType === 'bike' ? 'Superbike' : 'Car'));
    $serviceSlug = trim($input['service_slug'] ?? '');
    $serviceName = trim($input['service_name'] ?? ($serviceSlug ? ucwords(str_replace('-', ' ', $serviceSlug)) : 'Studio Consultation'));
    $message = trim($input['message'] ?? '');
    $source = trim($input['source'] ?? 'website_contact');

    if (empty($name) || empty($phone)) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'error' => ['message' => 'Name and phone are required']]);
        exit;
    }

    try {
        $db = Database::getConnection();
        $leadNo = 'LEAD-' . rand(1000, 9999);
        $now = date('Y-m-d H:i:s');

        $stmt = $db->prepare("
            INSERT INTO leads (
                lead_no, customer_name, phone, email, vehicle_brand, vehicle_model,
                service_name, estimated_amount, source, stage, remarks,
                quote_total, payment_status, created_at, updated_at
            ) VALUES (?, ?, ?, ?, 'Other', ?, ?, 0, ?, 'NEW', ?, 0, 'UNPAID', ?, ?)
        ");
        $stmt->execute([$leadNo, $name, $phone, $email ?: null, $vehicleModel, $serviceName, $source, $message, $now, $now]);
        $leadId = (int)$db->lastInsertId();

        // Dispatch to GoDaddy Business Mail
        dispatchGoDaddyEmail('lead', [
            'ref' => $leadNo,
            'lead_no' => $leadNo,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'vehicle_model' => $vehicleModel,
            'service_name' => $serviceName,
            'message' => $message,
            'source' => $source
        ]);

        echo json_encode(['ok' => true, 'data' => ['lead_id' => $leadId, 'ref' => $leadNo, 'message' => 'Your enquiry has been received.']]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => ['message' => $e->getMessage()]]);
    }
    exit;
}

if ($requestUri === '/api/leads' && $requestMethod === 'GET') {
    header('Content-Type: application/json');
    $leads = Database::query("SELECT id, lead_no as ref, customer_name, phone, email, vehicle_model, vehicle_reg, service_name, source, stage, created_at FROM leads ORDER BY created_at DESC");
    echo json_encode(['ok' => true, 'data' => $leads]);
    exit;
}

if ($requestUri === '/api/appointments' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $customer = $input['customer'] ?? [];
    $vehicle = $input['vehicle'] ?? [];
    $name = trim($customer['name'] ?? $input['name'] ?? '');
    $phone = trim($customer['phone'] ?? $input['phone'] ?? '');
    $email = trim($customer['email'] ?? $input['email'] ?? '');
    $serviceSlug = trim($input['service_slug'] ?? '');
    $serviceName = trim($input['service_name'] ?? ($serviceSlug ? ucwords(str_replace('-', ' ', $serviceSlug)) : 'Studio Detailing'));
    $date = trim($input['date'] ?? date('Y-m-d'));
    $time = trim($input['time'] ?? '10:00:00');
    $notes = trim($input['notes'] ?? '');
    $source = trim($input['source'] ?? 'booking_flow');

    if (empty($name) || empty($phone)) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'error' => ['message' => 'Customer name and phone are required']]);
        exit;
    }

    try {
        $db = Database::getConnection();
        $ref = 'DRC-' . rand(10000, 99999);
        $cleanTime = strlen($time) === 5 ? "{$time}:00" : $time;
        $now = date('Y-m-d H:i:s');

        // Customer check and insert
        $custRow = Database::queryOne("SELECT id FROM customers WHERE phone = ?", [$phone]);
        if ($custRow) {
            $customerId = (int)$custRow['id'];
        } else {
            $db->prepare("INSERT INTO customers (name, phone, whatsapp, email, created_at) VALUES (?, ?, ?, ?, ?)")
               ->execute([$name, $phone, $phone, $email ?: null, $now]);
            $customerId = (int)$db->lastInsertId();
        }

        // Vehicle
        $vBrand = $vehicle['brand'] ?? 'Car';
        $vModel = $vehicle['model'] ?? 'Standard';
        $vReg = $vehicle['registration'] ?? null;
        $vColor = $vehicle['color'] ?? null;
        $db->prepare("INSERT INTO vehicles (customer_id, type, brand, model, registration, color, created_at) VALUES (?, 'car', ?, ?, ?, ?, ?)")
           ->execute([$customerId, $vBrand, $vModel, $vReg, $vColor, $now]);
        $vehicleId = (int)$db->lastInsertId();

        // Appointment
        $db->prepare("INSERT INTO appointments (ref, customer_id, vehicle_id, date, time, status, notes, source, created_at) VALUES (?, ?, ?, ?, ?, 'CONFIRMED', ?, ?, ?)")
           ->execute([$ref, $customerId, $vehicleId, $date, $cleanTime, $notes, $source, $now]);

        // Lead record
        $db->prepare("INSERT INTO leads (lead_no, customer_name, phone, email, vehicle_model, vehicle_reg, service_name, source, stage, remarks, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'booking', 'NEW', ?, ?, ?)")
           ->execute([$ref, $name, $phone, $email ?: null, "{$vBrand} {$vModel}", $vReg, $serviceName, "Booking on {$date} at {$cleanTime}", $now, $now]);

        // Dispatch GoDaddy Business Mail
        dispatchGoDaddyEmail('booking', [
            'ref' => $ref,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'vehicle' => "{$vBrand} {$vModel} (" . ($vReg ?: 'N/A') . ")",
            'service' => $serviceName,
            'date' => $date,
            'time' => $cleanTime,
            'notes' => $notes,
            'source' => $source
        ]);

        echo json_encode([
            'ok' => true,
            'data' => [
                'ref' => $ref,
                'date' => $date,
                'time' => $cleanTime,
                'service_slug' => $serviceSlug,
                'service_name' => $serviceName,
                'customer_name' => $name,
                'customer_phone' => $phone
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => ['message' => $e->getMessage()]]);
    }
    exit;
}

if ($requestUri === '/api/appointments' && $requestMethod === 'GET') {
    header('Content-Type: application/json');
    $appointments = Database::query("
        SELECT a.ref, a.date, a.time, a.status, a.source, s.name as service_name, c.name as customer_name, c.phone, c.email, v.brand as vehicle_brand, v.model as vehicle_model, v.registration as vehicle_registration
        FROM appointments a
        LEFT JOIN services s ON a.service_id = s.id
        LEFT JOIN customers c ON a.customer_id = c.id
        LEFT JOIN vehicles v ON a.vehicle_id = v.id
        ORDER BY a.date DESC, a.time ASC
    ");
    echo json_encode(['ok' => true, 'data' => $appointments]);
    exit;
}

// ==========================================
// LEADS PIPELINE API & CSV EXPORT
// ==========================================
if ($requestUri === '/crm/api/leads/create' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $name = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $vehicleModel = trim($_POST['vehicle_model'] ?? '');
    $vehicleReg = trim($_POST['vehicle_reg'] ?? '');
    $serviceName = trim($_POST['service_name'] ?? 'Custom Detailing');
    $estimatedAmount = (float)($_POST['estimated_amount'] ?? 0);
    $source = trim($_POST['source'] ?? 'Walk-in');
    $stage = trim($_POST['stage'] ?? 'NEW');
    $remarks = trim($_POST['remarks'] ?? '');

    if (empty($name) || empty($phone) || empty($vehicleModel)) {
        echo json_encode(['success' => false, 'error' => 'Customer name, phone, and vehicle model are required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $leadNo = 'LEAD-' . date('Y') . '-' . sprintf('%03d', rand(100, 999));
        $now = date('Y-m-d H:i:s');

        // Insert lead
        $stmt = $db->prepare("
            INSERT INTO leads (
                lead_no, customer_name, phone, email, vehicle_model, vehicle_reg,
                service_name, estimated_amount, source, stage, remarks,
                quote_total, payment_status, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'UNPAID', ?, ?)
        ");
        $stmt->execute([
            $leadNo, $name, $phone, $email, $vehicleModel, $vehicleReg,
            $serviceName, $estimatedAmount, $source, $stage, $remarks,
            $estimatedAmount, $now, $now
        ]);

        // Dispatch GoDaddy notification
        dispatchGoDaddyEmail('lead', [
            'ref' => $leadNo,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'vehicle_model' => $vehicleModel,
            'service_name' => $serviceName,
            'message' => $remarks,
            'source' => $source
        ]);

        echo json_encode(['success' => true, 'message' => "Enquiry {$leadNo} created for {$name}!"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/leads/status' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $leadId = (int)($input['lead_id'] ?? 0);
    $stage = trim($input['stage'] ?? 'CONTACTED');

    if ($leadId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Valid lead ID required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $now = date('Y-m-d H:i:s');
        $stmt = $db->prepare("UPDATE leads SET stage = ?, updated_at = ? WHERE id = ?");
        $stmt->execute([$stage, $now, $leadId]);
        echo json_encode(['success' => true, 'message' => "Lead moved to {$stage} successfully!"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/leads/quote' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $leadId = (int)($input['lead_id'] ?? 0);
    $labor = (float)($input['labor_charge'] ?? 0);
    $discount = (float)($input['discount'] ?? 0);
    $remarks = trim($input['remarks'] ?? '');
    $consumables = $input['consumables'] ?? [];

    if ($leadId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Valid lead ID is required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $lead = Database::queryOne("SELECT * FROM leads WHERE id = ?", [$leadId]);
        if (!$lead) {
            echo json_encode(['success' => false, 'error' => 'Lead record not found']);
            exit;
        }

        $materialTotal = 0;
        $now = date('Y-m-d H:i:s');

        // Loop through consumables, deduct stock from inventory, and log OUT transaction
        foreach ($consumables as $item) {
            $itemId = (int)($item['item_id'] ?? 0);
            $qty = (float)($item['qty'] ?? 0);
            $unitCost = (float)($item['unit_cost'] ?? 0);
            $sellPrice = (float)($item['sell_price'] ?? $unitCost);

            if ($itemId > 0 && $qty > 0) {
                $materialTotal += ($qty * $sellPrice);

                // Deduct from store inventory
                $upStmt = $db->prepare("UPDATE inventory_items SET current_stock = MAX(0, current_stock - ?), updated_at = ? WHERE id = ?");
                $upStmt->execute([$qty, $now, $itemId]);

                // Record in inventory movement ledger
                $txStmt = $db->prepare("INSERT INTO inventory_transactions (item_id, type, quantity, unit_cost, total_cost, reference_no, reason, logged_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $txStmt->execute([
                    $itemId,
                    'OUT_JOB_CARD',
                    -$qty,
                    $unitCost,
                    -($qty * $unitCost),
                    $lead['lead_no'] ?: ('LEAD-' . $leadId),
                    "Allocated to {$lead['customer_name']} ({$lead['vehicle_model']}) for quote",
                    'Studio System',
                    $now
                ]);
            }
        }

        $subtotal = max(0, ($labor + $materialTotal) - $discount);
        $gst = round($subtotal * 0.18, 2);
        $grandTotal = $subtotal + $gst;

        $upLead = $db->prepare("
            UPDATE leads SET 
                stage = 'QUOTE_SENT',
                consumables_json = ?,
                quote_subtotal = ?,
                quote_discount = ?,
                quote_gst = ?,
                quote_total = ?,
                remarks = ?,
                updated_at = ?
            WHERE id = ?
        ");
        $upLead->execute([
            json_encode($consumables),
            $subtotal,
            $discount,
            $gst,
            $grandTotal,
            $remarks,
            $now,
            $leadId
        ]);

        echo json_encode([
            'success' => true,
            'message' => "Quotation generated (Grand Total: ₹" . number_format($grandTotal) . ") & store stock deducted!",
            'quote_total' => $grandTotal
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/leads/convert' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $leadId = (int)($_POST['lead_id'] ?? 0);
    $bayNo = (int)($_POST['bay_no'] ?? 1);
    $assignedTech = trim($_POST['assigned_tech'] ?? 'Lead Detailer');
    $date = trim($_POST['date'] ?? date('Y-m-d'));
    $time = trim($_POST['time'] ?? '10:00 AM');

    if ($leadId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Valid lead ID required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $lead = Database::queryOne("SELECT * FROM leads WHERE id = ?", [$leadId]);
        if (!$lead) {
            echo json_encode(['success' => false, 'error' => 'Lead not found']);
            exit;
        }

        $now = date('Y-m-d H:i:s');
        $up = $db->prepare("UPDATE leads SET stage = 'IN_SERVICE', bay_no = ?, assigned_tech = ?, updated_at = ? WHERE id = ?");
        $up->execute([$bayNo, $assignedTech, $now, $leadId]);

        echo json_encode(['success' => true, 'message' => "Vehicle dispatched to Bay 0{$bayNo}!"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/leads/payment' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $leadId = (int)($_POST['lead_id'] ?? 0);
    $status = trim($_POST['payment_status'] ?? 'PAID');
    $method = trim($_POST['payment_method'] ?? 'UPI');
    $paidAmount = (float)($_POST['paid_amount'] ?? 0);
    $stage = trim($_POST['stage'] ?? 'COMPLETED');

    if ($leadId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Valid lead ID required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $now = date('Y-m-d H:i:s');
        $lead = Database::queryOne("SELECT * FROM leads WHERE id = ?", [$leadId]);

        $up = $db->prepare("UPDATE leads SET payment_status = ?, payment_method = ?, paid_amount = ?, stage = ?, updated_at = ? WHERE id = ?");
        $up->execute([$status, $method, $paidAmount, $stage, $now, $leadId]);

        // Insert / link tax invoice
        if ($lead) {
            $invNo = 'INV-' . date('Y') . '-' . sprintf('%03d', rand(100, 999));
            $invSubtotal = (float)($lead['quote_subtotal'] ?: $lead['estimated_amount']);
            $invGst = (float)($lead['quote_gst'] ?: ($invSubtotal * 0.18));
            $invTotal = (float)($lead['quote_total'] ?: ($invSubtotal + $invGst));

            $invStmt = $db->prepare("
                INSERT INTO invoices (invoice_no, subtotal, discount, gst_amount, grand_total, payment_status, payment_method, paid_amount, notes, created_at)
                VALUES (?, ?, 0, ?, ?, ?, ?, ?, ?, ?)
            ");
            $invStmt->execute([
                $invNo, $invSubtotal, $invGst, $invTotal, $status, $method, $paidAmount,
                "Settled for {$lead['customer_name']} ({$lead['vehicle_model']})", $now
            ]);
        }

        echo json_encode(['success' => true, 'message' => "Payment recorded ({$method}) and job marked {$stage}!"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// CSV Export Endpoint
if ($requestUri === '/crm/export/leads') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="TheDriveClinic_Leads_Export_' . date('Y-m-d_His') . '.csv"');
    
    $out = fopen('php://output', 'w');
    // UTF-8 BOM for Excel compatibility
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Header row
    fputcsv($out, [
        'Lead ID',
        'Lead No',
        'Customer Name',
        'Phone Number',
        'Email Address',
        'Vehicle Brand',
        'Vehicle Model',
        'Registration Plate',
        'Service Requested',
        'Estimated Value (INR)',
        'Quoted Total (INR)',
        'Pipeline Stage',
        'Enquiry Source',
        'Payment Status',
        'Payment Method',
        'Paid Amount (INR)',
        'Assigned Technician',
        'Workshop Bay',
        'Consumables Allocated',
        'Customer Notes & Remarks',
        'Created Date'
    ]);

    $allLeads = Database::query("SELECT * FROM leads ORDER BY created_at DESC");
    foreach ($allLeads as $l) {
        $consumablesStr = '';
        if (!empty($l['consumables_json'])) {
            $parsed = json_decode($l['consumables_json'], true);
            if (is_array($parsed)) {
                $itemsArr = [];
                foreach ($parsed as $p) {
                    $itemsArr[] = ($p['qty'] ?? 1) . 'x ' . ($p['name'] ?? ($p['sku'] ?? 'Item'));
                }
                $consumablesStr = implode('; ', $itemsArr);
            }
        }

        fputcsv($out, [
            $l['id'],
            $l['lead_no'] ?: ('LEAD-' . $l['id']),
            $l['customer_name'],
            $l['phone'],
            $l['email'] ?: '—',
            $l['vehicle_brand'] ?: '—',
            $l['vehicle_model'],
            $l['vehicle_reg'] ?: '—',
            $l['service_name'],
            $l['estimated_amount'],
            $l['quote_total'] ?: $l['estimated_amount'],
            $l['stage'],
            $l['source'],
            $l['payment_status'] ?: 'UNPAID',
            $l['payment_method'] ?: '—',
            $l['paid_amount'] ?: 0,
            $l['assigned_tech'] ?: '—',
            $l['bay_no'] ? ('Bay 0' . $l['bay_no']) : '—',
            $consumablesStr ?: 'None',
            $l['remarks'] ?: '—',
            $l['created_at']
        ]);
    }
    fclose($out);
    exit;
}

// ==========================================
// CRM MEMBERSHIPS API & CSV EXPORT
// ==========================================
if ($requestUri === '/crm/api/memberships/create' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $name = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $vehicleModel = trim($_POST['vehicle_model'] ?? '');
    $vehicleReg = trim($_POST['vehicle_reg'] ?? '');
    $planName = trim($_POST['plan_name'] ?? 'Signature Wash Club');
    $billingCycle = trim($_POST['billing_cycle'] ?? 'Monthly');
    $price = (float)($_POST['price'] ?? 2999);
    $washesTotal = (int)($_POST['washes_total'] ?? ($billingCycle === 'Monthly' ? 3 : 6));
    $perks = trim($_POST['perks_notes'] ?? '');

    if (empty($name) || empty($phone) || empty($vehicleModel)) {
        echo json_encode(['success' => false, 'error' => 'Customer name, phone, and vehicle model are required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $memberNo = 'TDC-VIP-' . sprintf('%04d', rand(100, 9999));
        $startDate = date('Y-m-d');
        $expiryDate = ($billingCycle === 'Annual') ? date('Y-m-d', strtotime('+1 year')) : date('Y-m-d', strtotime('+30 days'));
        $now = date('Y-m-d H:i:s');

        // Insert membership record
        $stmt = $db->prepare("
            INSERT INTO memberships (
                member_no, customer_name, phone, email, vehicle_model, vehicle_reg,
                plan_name, billing_cycle, price, washes_total, washes_remaining,
                start_date, expiry_date, status, perks_notes, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ACTIVE', ?, ?, ?)
        ");
        $stmt->execute([
            $memberNo, $name, $phone, $email, $vehicleModel, $vehicleReg,
            $planName, $billingCycle, $price, $washesTotal, $washesTotal,
            $startDate, $expiryDate, $perks, $now, $now
        ]);

        // Sync customer into customers directory if new
        $cust = Database::queryOne("SELECT id FROM customers WHERE phone = ?", [$phone]);
        if ($cust) {
            $custId = (int)$cust['id'];
        } else {
            $cStmt = $db->prepare("INSERT INTO customers (name, phone, whatsapp, email, created_at) VALUES (?, ?, ?, ?, ?)");
            $cStmt->execute([$name, $phone, $phone, $email, $now]);
            $custId = (int)$db->lastInsertId();
        }

        // Sync vehicle
        $vCheck = Database::queryOne("SELECT id FROM vehicles WHERE customer_id = ? AND model = ?", [$custId, $vehicleModel]);
        if (!$vCheck) {
            $vStmt = $db->prepare("INSERT INTO vehicles (customer_id, type, brand, model, registration, created_at) VALUES (?, 'Car', 'Vehicle', ?, ?, ?)");
            $vStmt->execute([$custId, $vehicleModel, $vehicleReg, $now]);
        }

        echo json_encode([
            'success' => true,
            'message' => "VIP Member pass {$memberNo} activated for {$name}!",
            'member_no' => $memberNo
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/memberships/redeem' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $memberId = (int)($input['member_id'] ?? 0);

    if ($memberId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Valid member ID is required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $m = Database::queryOne("SELECT * FROM memberships WHERE id = ?", [$memberId]);
        if (!$m) {
            echo json_encode(['success' => false, 'error' => 'Member not found']);
            exit;
        }

        if ((int)$m['washes_remaining'] <= 0) {
            echo json_encode(['success' => false, 'error' => 'No wash passes remaining in this cycle']);
            exit;
        }

        $now = date('Y-m-d H:i:s');
        $newWashes = max(0, (int)$m['washes_remaining'] - 1);
        $up = $db->prepare("UPDATE memberships SET washes_remaining = ?, updated_at = ? WHERE id = ?");
        $up->execute([$newWashes, $now, $memberId]);

        echo json_encode([
            'success' => true,
            'message' => "1 Wash redeemed for {$m['customer_name']}! ({$newWashes} remaining)"
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/api/memberships/renew' && $requestMethod === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $memberId = (int)($input['member_id'] ?? 0);

    if ($memberId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Valid member ID is required']);
        exit;
    }

    try {
        $db = Database::getConnection();
        $m = Database::queryOne("SELECT * FROM memberships WHERE id = ?", [$memberId]);
        if (!$m) {
            echo json_encode(['success' => false, 'error' => 'Member not found']);
            exit;
        }

        $now = date('Y-m-d H:i:s');
        $currentExpiry = strtotime($m['expiry_date']);
        $baseTime = ($currentExpiry > time()) ? $currentExpiry : time();
        $newExpiry = ($m['billing_cycle'] === 'Annual') ? date('Y-m-d', strtotime('+1 year', $baseTime)) : date('Y-m-d', strtotime('+30 days', $baseTime));
        $resetWashes = (int)$m['washes_total'];

        $up = $db->prepare("UPDATE memberships SET expiry_date = ?, washes_remaining = ?, status = 'ACTIVE', updated_at = ? WHERE id = ?");
        $up->execute([$newExpiry, $resetWashes, $now, $memberId]);

        echo json_encode([
            'success' => true,
            'message' => "Membership for {$m['customer_name']} renewed until " . date('d M Y', strtotime($newExpiry)) . "!"
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($requestUri === '/crm/export/memberships') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="TheDriveClinic_Memberships_Export_' . date('Y-m-d_His') . '.csv"');
    
    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($out, [
        'Member ID',
        'Member Pass No',
        'Customer Name',
        'Phone Number',
        'Email Address',
        'Vehicle Model',
        'Registration Plate',
        'Club Plan Name',
        'Billing Cycle',
        'Subscription Price (INR)',
        'Total Wash Allowance',
        'Washes Remaining',
        'Start Date',
        'Expiry Date',
        'Status',
        'Perks & Notes',
        'Created Date'
    ]);

    $allMembers = Database::query("SELECT * FROM memberships ORDER BY created_at DESC");
    foreach ($allMembers as $m) {
        fputcsv($out, [
            $m['id'],
            $m['member_no'],
            $m['customer_name'],
            $m['phone'],
            $m['email'] ?: '—',
            $m['vehicle_model'],
            $m['vehicle_reg'] ?: '—',
            $m['plan_name'],
            $m['billing_cycle'],
            $m['price'],
            $m['washes_total'],
            $m['washes_remaining'],
            $m['start_date'],
            $m['expiry_date'],
            $m['status'],
            $m['perks_notes'] ?: '—',
            $m['created_at']
        ]);
    }
    fclose($out);
    exit;
}


// Studio Authentication Routes
if ($requestUri === '/crm/login') {
    $loginError = '';
    if ($requestMethod === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $pass = trim($_POST['password'] ?? '');

        $user = Database::queryOne("SELECT * FROM users WHERE email = ?", [$email]);
        if ($user && password_verify($pass, $user['password_hash'])) {
            $_SESSION['crm_user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            header('Location: /crm');
            exit;
        } else {
            // Studio master admin access authentication (Strict password: Thedriveclinic@2026)
            $validEmails = ['care@thedriveclinic.com', 'owner@thedriveclinic.com', 'admin@thedriveclinic.com', 'admin', 'care', 'owner'];
            $lowerEmail = strtolower($email);
            if ((in_array($lowerEmail, $validEmails) || strpos($lowerEmail, 'thedriveclinic.com') !== false) && $pass === 'Thedriveclinic@2026') {
                $_SESSION['crm_user'] = [
                    'id' => 1,
                    'name' => (strpos($lowerEmail, 'care') !== false ? 'Care Desk / Studio Admin' : 'The Drive Clinic Owner'),
                    'email' => $email,
                    'role' => 'owner'
                ];
                header('Location: /crm');
                exit;
            }
            $loginError = 'Invalid email address or password. Please try again.';
        }
    }

    require dirname(__DIR__) . '/templates/crm/login.php';
    exit;
}

if ($requestUri === '/crm/logout') {
    unset($_SESSION['crm_user']);
    session_destroy();
    header('Location: /crm/login');
    exit;
}

// ==========================================
// CRM WEB ROUTES
// ==========================================
if ($requestUri === '/crm' || $requestUri === '/crm/overview') {
    renderCrm('overview', [
        'pageTitle'    => 'Studio Overview | The Drive Clinic CRM',
        'activeNav'    => 'overview',
        'crumbSection' => 'Workspace',
        'crumbPage'    => 'Overview'
    ]);
}

if ($requestUri === '/crm/inventory') {
    renderCrm('inventory', [
        'pageTitle'    => 'Inventory & Decline Management | The Drive Clinic CRM',
        'activeNav'    => 'inventory',
        'crumbSection' => 'Operations',
        'crumbPage'    => 'Inventory & Store'
    ]);
}

if ($requestUri === '/crm/job-cards') {
    renderCrm('job-cards', [
        'pageTitle'    => 'Workshop Bay Job Cards | The Drive Clinic CRM',
        'activeNav'    => 'job-cards',
        'crumbSection' => 'Workspace',
        'crumbPage'    => 'Job Cards'
    ]);
}

if ($requestUri === '/crm/bookings') {
    renderCrm('bookings', [
        'pageTitle'    => 'Studio Bookings & Scheduler | The Drive Clinic CRM',
        'activeNav'    => 'bookings',
        'crumbSection' => 'Workspace',
        'crumbPage'    => 'Bookings'
    ]);
}

if ($requestUri === '/crm/customers') {
    renderCrm('customers', [
        'pageTitle'    => 'Customer & Vehicle Garage Directory | The Drive Clinic CRM',
        'activeNav'    => 'customers',
        'crumbSection' => 'Workspace',
        'crumbPage'    => 'Customers & Vehicles'
    ]);
}

if ($requestUri === '/crm/invoices') {
    renderCrm('invoices', [
        'pageTitle'    => 'Quotations & Invoices | The Drive Clinic CRM',
        'activeNav'    => 'invoices',
        'crumbSection' => 'Business',
        'crumbPage'    => 'Quotations & Invoices'
    ]);
}

if ($requestUri === '/crm/warranty') {
    renderCrm('warranty', [
        'pageTitle'    => 'Digital Warranty & PPF Registry | The Drive Clinic CRM',
        'activeNav'    => 'warranty',
        'crumbSection' => 'Business',
        'crumbPage'    => 'Warranty & PPF'
    ]);
}

if ($requestUri === '/crm/leads') {
    renderCrm('leads', [
        'pageTitle'    => 'Leads & Enquiries Pipeline | The Drive Clinic CRM',
        'activeNav'    => 'leads',
        'crumbSection' => 'Workspace',
        'crumbPage'    => 'Leads Pipeline'
    ]);
}

if ($requestUri === '/crm/services') {
    renderCrm('services', [
        'pageTitle'    => 'Services & Studio Packages | The Drive Clinic CRM',
        'activeNav'    => 'services',
        'crumbSection' => 'Business',
        'crumbPage'    => 'Services & Offers'
    ]);
}

if ($requestUri === '/crm/payments') {
    renderCrm('payments', [
        'pageTitle'    => 'Payment & Expense Ledger | The Drive Clinic CRM',
        'activeNav'    => 'payments',
        'crumbSection' => 'Finance',
        'crumbPage'    => 'Payment & Expense Ledger'
    ]);
}

if ($requestUri === '/crm/memberships') {
    renderCrm('memberships', [
        'pageTitle'    => 'Studio Club Memberships & VIP Passes | The Drive Clinic CRM',
        'activeNav'    => 'memberships',
        'crumbSection' => 'Clients & Billing',
        'crumbPage'    => 'Memberships'
    ]);
}

if ($requestUri === '/crm/team') {
    renderCrm('team', [
        'pageTitle'    => 'Technicians & Salary Payroll | The Drive Clinic CRM',
        'activeNav'    => 'team',
        'crumbSection' => 'Finance & Team',
        'crumbPage'    => 'Salary & Team'
    ]);
}

if ($requestUri === '/crm/follow-ups') {
    renderCrm('generic', [
        'pageTitle'    => 'Automated Follow-ups & Reminders | The Drive Clinic CRM',
        'activeNav'    => 'follow-ups',
        'crumbSection' => 'Operations',
        'crumbPage'    => 'Follow-ups',
        'moduleTitle'  => 'Post-Service Follow-ups & Reminders',
        'moduleDesc'   => '6-month ceramic maintenance and 1-year PPF inspection notifications.',
        'moduleBadge'  => 'OPERATIONS'
    ]);
}

if ($requestUri === '/crm/whatsapp') {
    renderCrm('whatsapp', [
        'pageTitle'    => 'WhatsApp Marketing Hub & Broadcasts | The Drive Clinic CRM',
        'activeNav'    => 'whatsapp',
        'crumbSection' => 'Growth',
        'crumbPage'    => 'WhatsApp Campaigns'
    ]);
}

if ($requestUri === '/crm/analytics' || $requestUri === '/crm/revenue') {
    renderCrm('analytics', [
        'pageTitle'    => 'Executive Revenue CRM & Profit Ledger | The Drive Clinic CRM',
        'activeNav'    => 'analytics',
        'crumbSection' => 'Finance',
        'crumbPage'    => 'Revenue CRM'
    ]);
}

// 404 Fallback
notFound();
