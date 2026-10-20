<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Digital PPF & Ceramic Warranty Certificate
$certId = (int)($certId ?? ($_GET['id'] ?? 0));
$cert = Database::queryOne("
    SELECT w.*, 
           c.name as cust_name, c.phone as cust_phone, c.email as cust_email,
           v.brand, v.model, v.registration, v.color
    FROM warranty_certificates w
    LEFT JOIN customers c ON w.customer_id = c.id
    LEFT JOIN vehicles v ON w.vehicle_id = v.id
    WHERE w.id = ?
", [$certId]);

if (!$cert) {
    echo "<div style='font-family:sans-serif; text-align:center; padding:60px;'><h3>Warranty certificate not found.</h3><a href='/crm/warranty'>Back to Registry</a></div>";
    exit;
}

$customerName = $cert['customer_name'] ?: ($cert['cust_name'] ?? 'Client');
$customerPhone = $cert['customer_phone'] ?: ($cert['cust_phone'] ?? '—');
$vehicleModel = $cert['vehicle_model'] ?: trim(($cert['brand'] ?? '') . ' ' . ($cert['model'] ?? 'Vehicle'));
$vehicleReg = $cert['vehicle_reg'] ?: ($cert['registration'] ?? 'Pending Reg');
$productBrand = $cert['product_brand'] ?: 'STEK DYNOshield Self-Healing TPU';
$batchNo = $cert['roll_batch_no'] ?: 'STK-2026-B849';
$years = (int)($cert['warranty_years'] ?: 5);
$scope = $cert['coverage_scope'] ?: 'Full Exterior Paint Protection Film';
$installDate = $cert['install_date'] ? date('d M Y', strtotime($cert['install_date'])) : date('d M Y');
$expiryDate = $cert['expiry_date'] ? date('d M Y', strtotime($cert['expiry_date'])) : date('d M Y', strtotime("+{$years} years"));
$nextInsp = $cert['next_inspection_date'] ? date('d M Y', strtotime($cert['next_inspection_date'])) : date('d M Y', strtotime('+6 months'));

// WhatsApp Share Link
$siteUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$certUrl = "{$siteUrl}/crm/warranty/{$cert['id']}/print";
$cleanPhone = preg_replace('/\D/', '', $customerPhone);
if (strlen($cleanPhone) === 10) $cleanPhone = '91' . $cleanPhone;

$waText = urlencode(
    "🛡️ *THE DRIVE CLINIC — OFFICIAL PPF WARRANTY CERTIFICATE* 🛡️\n\n" .
    "Dear {$customerName},\n" .
    "Congratulations! Your *{$vehicleModel}* ({$vehicleReg}) has been successfully enrolled in The Drive Clinic Digital Warranty Registry.\n\n" .
    "📜 *Certificate No:* #{$cert['cert_no']}\n" .
    "🛡️ *Product Film:* {$productBrand} (Batch: {$batchNo})\n" .
    "⏱️ *Warranty Duration:* {$years} Years (Valid until {$expiryDate})\n" .
    "🔍 *Next Inspection / Top-Up:* {$nextInsp}\n\n" .
    "📄 *View & Download Official Digital Certificate:* {$certUrl}\n\n" .
    "Please present this certificate during your scheduled 6-month inspection.\n\n" .
    "The Drive Clinic • Jammu's Premier Detailing Studio • +91 94191 00000"
);
$waShareLink = "https://wa.me/{$cleanPhone}?text={$waText}";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Warranty Certificate <?= htmlspecialchars($cert['cert_no']) ?> — THE DRIVE CLINIC</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Cinzel:wght@500;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --gold: #C5A059;
      --gold-dark: #8C6D2D;
      --ink: #0A0D14;
      --red: #E11D48;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: #06080D;
      color: #E2E8F0;
      font-family: 'Space Grotesk', sans-serif;
      padding: 30px 15px;
    }
    .print-actions-bar {
      max-width: 900px;
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
    .btn-gold { background: linear-gradient(135deg, #DFBA73 0%, #B88E3E 100%); color: #000; font-weight: 800; }
    .btn-gold:hover { filter: brightness(1.1); }
    .btn-wa { background: #25D366; color: #fff; }
    .btn-wa:hover { background: #1eb956; }
    .btn-back { background: rgba(255,255,255,0.1); color: #fff; }

    /* Certificate Luxury Board */
    .cert-sheet {
      max-width: 900px;
      margin: 0 auto;
      background: #0E121B;
      border: 3px solid var(--gold);
      border-radius: 12px;
      padding: 54px 60px;
      position: relative;
      box-shadow: 0 25px 60px rgba(0,0,0,0.8), inset 0 0 100px rgba(197, 160, 89, 0.05);
      color: #FFFFFF;
    }
    .cert-border-inner {
      border: 1px solid rgba(197, 160, 89, 0.35);
      padding: 36px 40px;
      border-radius: 8px;
      position: relative;
    }
    .cert-top-crest {
      text-align: center;
      margin-bottom: 24px;
    }
    .cert-studio-title {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 44px;
      letter-spacing: 0.08em;
      color: #FFFFFF;
      margin-bottom: 2px;
    }
    .cert-studio-title span { color: var(--red); }
    .cert-sub-text {
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.25em;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 18px;
    }
    .cert-heading {
      font-family: 'Cinzel', serif;
      font-size: 26px;
      font-weight: 700;
      letter-spacing: 0.12em;
      color: #FFFFFF;
      text-transform: uppercase;
      text-align: center;
      margin-bottom: 8px;
    }
    .cert-number {
      font-family: monospace;
      font-size: 14px;
      font-weight: 700;
      color: var(--gold);
      text-align: center;
      letter-spacing: 0.15em;
      margin-bottom: 30px;
    }

    /* Core Beneficiary Box */
    .cert-statement {
      text-align: center;
      font-size: 15px;
      color: #CBD5E1;
      max-width: 680px;
      margin: 0 auto 30px;
      line-height: 1.7;
    }
    .cert-owner-name {
      font-family: 'Cinzel', serif;
      font-size: 28px;
      color: var(--gold);
      font-weight: 700;
      display: block;
      margin: 6px 0;
      text-transform: uppercase;
    }

    /* Specs Grid */
    .cert-specs-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
      background: rgba(255,255,255,0.03);
      border: 1px solid rgba(197, 160, 89, 0.2);
      border-radius: 8px;
      padding: 24px 28px;
      margin-bottom: 32px;
    }
    .spec-item {
      display: flex;
      flex-direction: column;
      gap: 3px;
    }
    .spec-label {
      font-size: 10px;
      font-weight: 800;
      letter-spacing: 0.15em;
      color: var(--gold);
      text-transform: uppercase;
    }
    .spec-val {
      font-size: 15px;
      font-weight: 700;
      color: #FFFFFF;
    }
    .spec-val-plate {
      font-family: monospace;
      background: #FEF08A;
      color: #713F12;
      padding: 2px 8px;
      border-radius: 4px;
      display: inline-block;
      width: fit-content;
      font-size: 13px;
    }

    /* Bottom Signatures & Seal */
    .cert-bottom-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      padding-top: 24px;
      border-top: 1px solid rgba(197, 160, 89, 0.25);
    }
    .cert-seal-badge {
      width: 90px;
      height: 90px;
      border: 2px double var(--gold);
      border-radius: 50%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      font-family: 'Cinzel', serif;
      font-size: 9px;
      color: var(--gold);
      letter-spacing: 0.1em;
      line-height: 1.2;
      transform: rotate(-8deg);
      box-shadow: 0 0 20px rgba(197, 160, 89, 0.15);
    }
    .cert-sig-block {
      text-align: right;
    }
    .sig-line {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 14px;
      font-weight: 700;
      color: #FFFFFF;
    }
    .sig-title {
      font-size: 11px;
      color: #94A3B8;
      letter-spacing: 0.05em;
    }

    @media print {
      body {
        background: #FFFFFF !important;
        padding: 0;
      }
      .print-actions-bar {
        display: none !important;
      }
      .cert-sheet {
        background: #0E121B !important;
        box-shadow: none !important;
        border: 2px solid #8C6D2D !important;
        padding: 30px !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
    }
  </style>
</head>
<body>

  <div class="print-actions-bar">
    <a href="/crm/warranty" class="btn btn-back">← Back to Warranty Registry</a>
    <div style="display:flex; gap:10px;">
      <a href="<?= $waShareLink ?>" target="_blank" rel="noopener" class="btn btn-wa">
        💬 Share Certificate on WhatsApp
      </a>
      <button onclick="window.print()" class="btn btn-gold">
        🖨️ Print Certificate / PDF
      </button>
    </div>
  </div>

  <div class="cert-sheet">
    <div class="cert-border-inner">
      <div class="cert-top-crest">
        <div class="cert-studio-title">THE DRIVE <span>CLINIC</span></div>
        <div class="cert-sub-text">OFFICIAL AUTOMOTIVE SURFACE ARMOR REGISTRY • JAMMU</div>
        <div class="cert-heading">Certificate of Warranty & Authenticity</div>
        <div class="cert-number">REGISTRY # <?= htmlspecialchars($cert['cert_no']) ?></div>
      </div>

      <div class="cert-statement">
        This certifies that the vehicle listed below has undergone master surface decontamination, precision film application, and rigorous optical inspection at The Drive Clinic Flagship Studio in Jammu. Full manufacturer warranty coverage is active under:
        <span class="cert-owner-name"><?= htmlspecialchars($customerName) ?></span>
      </div>

      <div class="cert-specs-grid">
        <div class="spec-item">
          <span class="spec-label">VEHICLE MAKE & MODEL</span>
          <span class="spec-val"><?= htmlspecialchars($vehicleModel) ?></span>
        </div>
        <div class="spec-item">
          <span class="spec-label">REGISTRATION PLATE</span>
          <span class="spec-val"><span class="spec-val-plate"><?= htmlspecialchars($vehicleReg) ?></span></span>
        </div>
        <div class="spec-item">
          <span class="spec-label">FILM / COATING PRODUCT</span>
          <span class="spec-val"><?= htmlspecialchars($productBrand) ?></span>
        </div>
        <div class="spec-item">
          <span class="spec-label">MANUFACTURER ROLL BATCH #</span>
          <span class="spec-val" style="font-family:monospace; color:var(--gold);"><?= htmlspecialchars($batchNo) ?></span>
        </div>
        <div class="spec-item">
          <span class="spec-label">COVERAGE SCOPE</span>
          <span class="spec-val"><?= htmlspecialchars($scope) ?></span>
        </div>
        <div class="spec-item">
          <span class="spec-label">WARRANTY TERM & DURATION</span>
          <span class="spec-val" style="color:#10b981;"><?= $years ?> YEARS (Expires: <?= $expiryDate ?>)</span>
        </div>
        <div class="spec-item">
          <span class="spec-label">INSTALLATION DATE</span>
          <span class="spec-val"><?= $installDate ?></span>
        </div>
        <div class="spec-item">
          <span class="spec-label">NEXT MANDATORY INSPECTION</span>
          <span class="spec-val" style="color:var(--gold);"><?= $nextInsp ?></span>
        </div>
      </div>

      <div class="cert-bottom-row">
        <div class="cert-seal-badge">
          <span>THE DRIVE CLINIC</span>
          <strong style="font-size:11px; margin:2px 0;">OFFICIAL</strong>
          <span>SEAL &bull; 2026</span>
        </div>

        <div style="font-size:11px; color:#64748B; max-width:320px; line-height:1.5;">
          *Covers bubbling, yellowing, delamination, and adhesive failure under normal use. 6-Month maintenance inspection required at studio.
        </div>

        <div class="cert-sig-block">
          <div class="sig-line">Aman Verma / Master Tech</div>
          <div class="sig-title">Certified PPF Installation Director</div>
          <div style="font-size:10px; color:var(--gold); margin-top:2px;">The Drive Clinic Jammu</div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
