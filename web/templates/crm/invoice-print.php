<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Luxury Printable Tax Invoice View
$invoiceId = (int)($invoiceId ?? ($_GET['id'] ?? 0));
$invoice = Database::queryOne("
    SELECT i.*, 
           c.name as cust_name, c.phone as cust_phone, c.email as cust_email,
           v.brand, v.model, v.registration, v.color
    FROM invoices i
    LEFT JOIN customers c ON i.customer_id = c.id
    LEFT JOIN vehicles v ON i.vehicle_id = v.id
    WHERE i.id = ?
", [$invoiceId]);

if (!$invoice) {
    echo "<div style='font-family:sans-serif; text-align:center; padding:60px;'><h3>Invoice not found.</h3><a href='/crm/invoices'>Back to Invoices</a></div>";
    exit;
}

$customerName = $invoice['customer_name'] ?: ($invoice['cust_name'] ?? 'Valued Customer');
$customerPhone = $invoice['customer_phone'] ?: ($invoice['cust_phone'] ?? '—');
$customerEmail = $invoice['customer_email'] ?: ($invoice['cust_email'] ?? '—');
$vehicleModel = $invoice['vehicle_model'] ?: trim(($invoice['brand'] ?? '') . ' ' . ($invoice['model'] ?? 'Vehicle'));
$vehicleReg = $invoice['vehicle_reg'] ?: ($invoice['registration'] ?? 'Pending Reg');

$items = [];
if (!empty($invoice['items_json'])) {
    $items = json_decode($invoice['items_json'], true) ?: [];
}

if (empty($items)) {
    // Fallback if created without explicit items JSON
    $items = [
        [
            'name'  => $invoice['notes'] ?: 'Automotive Studio Detailing Service',
            'type'  => 'service',
            'qty'   => 1,
            'rate'  => (float)$invoice['subtotal'],
            'total' => (float)$invoice['subtotal']
        ]
    ];
}

$subtotal = (float)$invoice['subtotal'];
$discount = (float)$invoice['discount'];
$gstAmount = (float)$invoice['gst_amount'];
$grandTotal = (float)$invoice['grand_total'];
$cgst = $gstAmount > 0 ? round($gstAmount / 2, 2) : 0;
$sgst = $gstAmount > 0 ? round($gstAmount / 2, 2) : 0;
$paymentStatus = strtoupper($invoice['payment_status'] ?? 'PENDING');
$paymentMethod = $invoice['payment_method'] ?? 'UPI';

// WhatsApp Share URL
$siteUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$invoiceUrl = "{$siteUrl}/crm/invoices/{$invoice['id']}/print";
$cleanPhone = preg_replace('/\D/', '', $customerPhone);
if (strlen($cleanPhone) === 10) $cleanPhone = '91' . $cleanPhone;

$waText = urlencode(
    "⚡ *THE DRIVE CLINIC — STUDIO INVOICE* ⚡\n\n" .
    "Dear {$customerName},\n" .
    "Here is your official detailing tax invoice *#{$invoice['invoice_no']}* for your *{$vehicleModel}* ({$vehicleReg}).\n\n" .
    "💰 *Grand Total:* ₹" . number_format($grandTotal) . "\n" .
    "💳 *Payment Status:* {$paymentStatus} (" . ($paymentMethod ?: 'Pending') . ")\n\n" .
    "📄 *View / Download Invoice PDF:* {$invoiceUrl}\n\n" .
    "Thank you for choosing The Drive Clinic Jammu!\n" .
    "📍 Nanak Nagar, Jammu • 📞 +91 94191 00000"
);
$waShareLink = "https://wa.me/{$cleanPhone}?text={$waText}";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice <?= htmlspecialchars($invoice['invoice_no']) ?> — THE DRIVE CLINIC JAMMU</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --ink: #0B0E14;
      --red: #E11D48;
      --gold: #D97706;
      --muted: #64748B;
      --line: #E2E8F0;
      --bg-soft: #F8FAFC;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: #0B0E17;
      color: #1E293B;
      font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, sans-serif;
      padding: 30px 15px;
      font-size: 14px;
      line-height: 1.5;
    }
    .print-actions-bar {
      max-width: 860px;
      margin: 0 auto 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: 700;
      font-size: 13px;
      text-decoration: none;
      cursor: pointer;
      border: none;
      transition: all 0.2s;
    }
    .btn-print { background: var(--red); color: #fff; }
    .btn-print:hover { background: #be123c; }
    .btn-wa { background: #25D366; color: #fff; }
    .btn-wa:hover { background: #1eb956; }
    .btn-back { background: rgba(255,255,255,0.1); color: #fff; }
    .btn-back:hover { background: rgba(255,255,255,0.2); }

    /* Invoice Paper Container */
    .invoice-sheet {
      max-width: 860px;
      margin: 0 auto;
      background: #FFFFFF;
      border-radius: 10px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.5);
      padding: 48px;
      position: relative;
    }

    .inv-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 2px solid var(--ink);
      padding-bottom: 24px;
      margin-bottom: 28px;
    }
    .brand-title {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 38px;
      letter-spacing: 0.05em;
      color: var(--ink);
      line-height: 1;
      margin-bottom: 4px;
    }
    .brand-title span { color: var(--red); }
    .brand-sub {
      font-size: 11px;
      letter-spacing: 0.15em;
      font-weight: 700;
      color: var(--muted);
      text-transform: uppercase;
      margin-bottom: 12px;
    }
    .studio-meta {
      font-size: 12px;
      color: #475569;
      line-height: 1.5;
    }

    .inv-badge-block {
      text-align: right;
    }
    .inv-type-title {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 32px;
      letter-spacing: 0.05em;
      color: var(--ink);
      margin-bottom: 6px;
    }
    .inv-no {
      font-family: monospace;
      font-size: 16px;
      font-weight: 700;
      color: var(--red);
      margin-bottom: 4px;
    }
    .inv-date {
      font-size: 12px;
      color: var(--muted);
      margin-bottom: 10px;
    }
    .status-stamp {
      display: inline-block;
      padding: 4px 14px;
      border-radius: 4px;
      font-weight: 800;
      font-size: 11px;
      letter-spacing: 0.1em;
      text-transform: uppercase;
    }
    .stamp-paid {
      background: #DCFCE7;
      color: #15803D;
      border: 1px solid #86EFAC;
    }
    .stamp-pending {
      background: #FEF3C7;
      color: #B45309;
      border: 1px solid #FCD34D;
    }

    /* Client & Vehicle 2-Column Box */
    .party-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
      background: var(--bg-soft);
      border: 1px solid var(--line);
      border-radius: 8px;
      padding: 20px 24px;
      margin-bottom: 28px;
    }
    .party-col-title {
      font-size: 10px;
      font-weight: 800;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: var(--red);
      margin-bottom: 8px;
    }
    .party-name {
      font-size: 16px;
      font-weight: 700;
      color: var(--ink);
      margin-bottom: 4px;
    }
    .party-detail {
      font-size: 12px;
      color: #475569;
      line-height: 1.5;
    }
    .plate-badge {
      display: inline-block;
      background: #FEF08A;
      color: #854D0E;
      font-family: monospace;
      font-weight: 800;
      padding: 2px 8px;
      border-radius: 4px;
      border: 1px solid #EAB308;
      margin-top: 4px;
      font-size: 12px;
    }

    /* Items Table */
    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 28px;
    }
    .items-table th {
      background: #0B0E14;
      color: #FFFFFF;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      padding: 12px 14px;
      text-align: left;
    }
    .items-table th:last-child,
    .items-table td:last-child {
      text-align: right;
    }
    .items-table td {
      padding: 14px;
      border-bottom: 1px solid var(--line);
      font-size: 13px;
      color: #1E293B;
    }
    .items-table tbody tr:nth-child(even) {
      background: #F8FAFC;
    }
    .item-type-badge {
      font-size: 9px;
      font-weight: 800;
      padding: 2px 6px;
      border-radius: 3px;
      text-transform: uppercase;
      background: #E2E8F0;
      color: #475569;
      margin-left: 6px;
    }
    .type-service { background: #E0E7FF; color: #4338CA; }
    .type-product { background: #FEF3C7; color: #92400E; }

    /* Totals Summary Row */
    .totals-wrapper {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 32px;
      margin-bottom: 32px;
    }
    .bank-box {
      flex: 1;
      border: 1px solid var(--line);
      border-radius: 6px;
      padding: 16px 20px;
      background: var(--bg-soft);
      font-size: 11px;
      color: #475569;
      line-height: 1.6;
    }
    .bank-box strong { color: var(--ink); }

    .totals-box {
      width: 320px;
    }
    .totals-row {
      display: flex;
      justify-content: space-between;
      padding: 6px 0;
      font-size: 13px;
      color: #475569;
    }
    .totals-row.grand-total {
      border-top: 2px solid var(--ink);
      margin-top: 8px;
      padding-top: 10px;
      font-size: 18px;
      font-weight: 800;
      color: var(--ink);
    }
    .grand-total .amount-total {
      color: var(--red);
    }

    /* Terms & Signature */
    .inv-footer {
      border-top: 1px solid var(--line);
      padding-top: 20px;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      font-size: 11px;
      color: var(--muted);
    }
    .terms-col {
      max-width: 480px;
      line-height: 1.5;
    }
    .terms-col strong { color: var(--ink); display: block; margin-bottom: 4px; font-size: 11px; }
    .sig-col {
      text-align: center;
    }
    .sig-seal {
      width: 70px;
      height: 70px;
      border: 2px dashed var(--red);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      font-weight: 800;
      color: var(--red);
      text-align: center;
      margin: 0 auto 6px;
      transform: rotate(-10deg);
      line-height: 1.1;
    }
    .sig-name {
      font-weight: 700;
      color: var(--ink);
      font-size: 12px;
    }

    @media print {
      body {
        background: #FFFFFF;
        padding: 0;
      }
      .print-actions-bar {
        display: none !important;
      }
      .invoice-sheet {
        box-shadow: none !important;
        border-radius: 0;
        padding: 24px;
        max-width: 100%;
      }
    }
  </style>
</head>
<body>

  <!-- Controls Bar -->
  <div class="print-actions-bar">
    <a href="/crm/invoices" class="btn btn-back">← Back to Invoices</a>
    <div style="display:flex; gap:10px;">
      <a href="<?= $waShareLink ?>" target="_blank" rel="noopener" class="btn btn-wa">
        💬 Share on WhatsApp
      </a>
      <button onclick="window.print()" class="btn btn-print">
        🖨️ Print / Save PDF
      </button>
    </div>
  </div>

  <!-- Printable Invoice Document -->
  <div class="invoice-sheet">
    <!-- 1. Header -->
    <div class="inv-header">
      <div>
        <div class="brand-title">THE DRIVE <span>CLINIC</span></div>
        <div class="brand-sub">Luxury Automotive Detailing Studio • Jammu</div>
        <div class="studio-meta">
          <strong>Nanak Nagar, Jammu, Jammu & Kashmir 180004</strong><br>
          Phone: +91 94191 00000 • Email: care@thedriveclinic.com<br>
          GSTIN: 01AABCT1234F1Z8 (Jammu & Kashmir) • State Code: 01
        </div>
      </div>
      <div class="inv-badge-block">
        <div class="inv-type-title">TAX INVOICE</div>
        <div class="inv-no"># <?= htmlspecialchars($invoice['invoice_no']) ?></div>
        <div class="inv-date">Date: <?= date('d M Y', strtotime($invoice['created_at'])) ?></div>
        <div>
          <?php if ($paymentStatus === 'PAID'): ?>
            <span class="status-stamp stamp-paid">✓ PAID IN FULL (<?= htmlspecialchars($paymentMethod) ?>)</span>
          <?php else: ?>
            <span class="status-stamp stamp-pending">⚠️ PAYMENT DUE</span>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- 2. Client & Vehicle Details -->
    <div class="party-grid">
      <div>
        <div class="party-col-title">BILLED TO (CLIENT DETAILS)</div>
        <div class="party-name"><?= htmlspecialchars($customerName) ?></div>
        <div class="party-detail">
          <strong>Mobile:</strong> <?= htmlspecialchars($customerPhone) ?><br>
          <strong>Email:</strong> <?= htmlspecialchars($customerEmail) ?><br>
          <strong>Location:</strong> Jammu & Kashmir, India
        </div>
      </div>
      <div>
        <div class="party-col-title">VEHICLE & GARAGE SPECIFICATION</div>
        <div class="party-name"><?= htmlspecialchars($vehicleModel) ?></div>
        <div class="party-detail">
          <strong>Registration:</strong> <span class="plate-badge"><?= htmlspecialchars($vehicleReg) ?></span><br>
          <strong>Color / Spec:</strong> <?= htmlspecialchars($invoice['color'] ?? 'Factory OEM Finish') ?><br>
          <strong>Workshop Bay:</strong> Bay 01 — Dust-Free Detailing Bay
        </div>
      </div>
    </div>

    <!-- 3. Line Items Table -->
    <table class="items-table">
      <thead>
        <tr>
          <th style="width: 45px;">#</th>
          <th>Description of Service / Product</th>
          <th style="width: 90px; text-align:center;">Type</th>
          <th style="width: 70px; text-align:center;">Qty</th>
          <th style="width: 120px; text-align:right;">Rate (₹)</th>
          <th style="width: 130px; text-align:right;">Amount (₹)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $idx => $it): 
          $qty = (float)($it['qty'] ?? 1);
          $rate = (float)($it['rate'] ?? $it['price'] ?? 0);
          $rowTotal = (float)($it['total'] ?? ($qty * $rate));
          $type = strtolower($it['type'] ?? 'service');
        ?>
        <tr>
          <td><?= sprintf('%02d', $idx + 1) ?></td>
          <td>
            <strong><?= htmlspecialchars($it['name']) ?></strong>
          </td>
          <td style="text-align:center;">
            <span class="item-type-badge <?= $type === 'service' ? 'type-service' : 'type-product' ?>">
              <?= htmlspecialchars($type) ?>
            </span>
          </td>
          <td style="text-align:center;"><?= $qty ?></td>
          <td style="text-align:right;">₹<?= number_format($rate, 2) ?></td>
          <td style="text-align:right;"><strong>₹<?= number_format($rowTotal, 2) ?></strong></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- 4. Summary & Bank Info -->
    <div class="totals-wrapper">
      <div class="bank-box">
        <strong style="font-size:12px; display:block; margin-bottom:6px; color:var(--red);">DIGITAL SETTLEMENT & UPI:</strong>
        <strong>UPI ID:</strong> thedriveclinic@upi<br>
        <strong>Bank:</strong> HDFC Bank Ltd, Nanak Nagar Branch<br>
        <strong>Account Name:</strong> The Drive Clinic Studio Jammu<br>
        <strong>A/C No:</strong> 50200088192831 • <strong>IFSC:</strong> HDFC0001234<br>
        <div style="margin-top:6px; font-size:10px; color:#64748B;">Instant UPI payment receipts will be confirmed via studio SMS / WhatsApp.</div>
      </div>

      <div class="totals-box">
        <div class="totals-row">
          <span>Subtotal:</span>
          <span>₹<?= number_format($subtotal, 2) ?></span>
        </div>
        <?php if ($discount > 0): ?>
        <div class="totals-row" style="color:#10b981;">
          <span>Studio Privilege Discount:</span>
          <span>- ₹<?= number_format($discount, 2) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($gstAmount > 0): ?>
        <div class="totals-row">
          <span>CGST (9.0%):</span>
          <span>₹<?= number_format($cgst, 2) ?></span>
        </div>
        <div class="totals-row">
          <span>SGST (9.0%):</span>
          <span>₹<?= number_format($sgst, 2) ?></span>
        </div>
        <?php endif; ?>
        <div class="totals-row grand-total">
          <span>Grand Total:</span>
          <span class="amount-total">₹<?= number_format($grandTotal) ?></span>
        </div>
        <div class="totals-row" style="font-size:11px; margin-top:4px;">
          <span>Paid Amount:</span>
          <strong>₹<?= number_format((float)($invoice['paid_amount'] ?: ($paymentStatus === 'PAID' ? $grandTotal : 0))) ?></strong>
        </div>
      </div>
    </div>

    <!-- 5. Footer & Terms -->
    <div class="inv-footer">
      <div class="terms-col">
        <strong>TERMS & WARRANTY CONDITIONS:</strong>
        1. All ceramic coatings and PPF applications carry authentic digital warranty coverage registered in the studio database.<br>
        2. Maintenance wash guidelines must be strictly adhered to using pH-neutral automotive shampoos.<br>
        3. This invoice serves as official tax receipt and proof of installation for manufacturer warranty claims.
      </div>
      <div class="sig-col">
        <div class="sig-seal">THE DRIVE<br>CLINIC<br>SEAL</div>
        <div class="sig-name">Authorized Signatory</div>
        <div style="font-size:10px; color:var(--muted);">Studio Manager • Jammu</div>
      </div>
    </div>
  </div>

</body>
</html>
