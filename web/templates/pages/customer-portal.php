<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Customer & VIP Garage Self-Service Portal (`/portal` or `/customer-panel`)
$searchQuery = trim($_GET['q'] ?? '');
$customer = null;
$vehicles = [];
$invoices = [];
$jobCards = [];
$warranties = [];
$membership = null;

if (!empty($searchQuery)) {
    $cleanQuery = preg_replace('/[^a-zA-Z0-9]/', '', $searchQuery);
    
    // Search by phone, email, or vehicle reg
    $customer = Database::queryOne("
        SELECT * FROM customers 
        WHERE phone LIKE ? OR whatsapp LIKE ? OR email LIKE ?
        LIMIT 1
    ", ["%{$cleanQuery}%", "%{$cleanQuery}%", "%{$cleanQuery}%"]);

    if (!$customer) {
        // Try searching by vehicle registration
        $veh = Database::queryOne("
            SELECT * FROM vehicles 
            WHERE REPLACE(REPLACE(UPPER(registration), ' ', ''), '-', '') LIKE ?
            LIMIT 1
        ", ["%{$cleanQuery}%"]);
        if ($veh) {
            $customer = Database::queryOne("SELECT * FROM customers WHERE id = ?", [$veh['customer_id']]);
        }
    }

    if ($customer) {
        $custId = (int)$customer['id'];
        $vehicles = Database::query("SELECT * FROM vehicles WHERE customer_id = ? ORDER BY id DESC", [$custId]);
        $invoices = Database::query("SELECT * FROM invoices WHERE customer_id = ? OR customer_phone = ? ORDER BY created_at DESC", [$custId, $customer['phone']]);
        $jobCards = Database::query("SELECT * FROM job_cards WHERE customer_id = ? ORDER BY created_at DESC", [$custId]);
        $warranties = Database::query("SELECT * FROM warranty_certificates WHERE customer_id = ? OR customer_phone = ? ORDER BY created_at DESC", [$custId, $customer['phone']]);
        $membership = Database::queryOne("SELECT * FROM memberships WHERE phone = ? OR customer_name = ? ORDER BY id DESC LIMIT 1", [$customer['phone'], $customer['name']]);
    }
}
?>

<div class="portal-page-container" style="min-height: 100vh; background-color: var(--black); color: var(--white); padding-top: 100px; padding-bottom: 80px;">
  <div class="container">
    
    <!-- Header -->
    <div style="text-align:center; max-width:760px; margin: 0 auto 40px;">
      <div class="eyebrow" style="color:var(--red); justify-content:center;">DIGITAL CONCIERGE & GARAGE</div>
      <h1 class="h1-hero" style="font-size:clamp(40px, 4.5vw, 64px); margin-bottom:14px;">
        MY STUDIO <span class="text-red">GARAGE.</span>
      </h1>
      <p class="lead-text" style="color:var(--grey); font-size:16px;">
        Track live bay detailing progress, view GST tax invoices, verify digital PPF warranty certificates, and manage VIP club wash passes.
      </p>
    </div>

    <!-- Search / Phone Lookup Card -->
    <div style="max-width: 640px; margin: 0 auto 48px; background: rgba(21, 26, 38, 0.85); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; padding: 28px 32px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
      <form action="/portal" method="GET" style="display:flex; gap:12px; flex-wrap:wrap;">
        <input 
          type="text" 
          name="q" 
          value="<?= htmlspecialchars($searchQuery) ?>" 
          placeholder="Enter 10-digit mobile number or vehicle reg..." 
          required 
          style="flex: 1; min-width: 240px; background: #0B0E14; border: 1px solid rgba(255,255,255,0.16); color: #fff; padding: 14px 18px; border-radius: 6px; font-size: 15px; font-weight:600;"
        >
        <button type="submit" class="btn btn-red" style="padding:14px 28px;">
          LOOKUP GARAGE →
        </button>
      </form>
      <div style="margin-top: 14px; display:flex; align-items:center; gap:8px; font-size:12px; color:var(--text-3);">
        <span>Quick Test Profiles:</span>
        <a href="/portal?q=9419122334" style="color:var(--red); text-decoration:underline;">Vikram Sharma (9419122334)</a>
        <span>•</span>
        <a href="/portal?q=9419277881" style="color:var(--red); text-decoration:underline;">Gurpreet Singh (Thar)</a>
      </div>
    </div>

    <?php if (!empty($searchQuery) && !$customer): ?>
      <div style="max-width: 640px; margin: 0 auto; text-align: center; padding: 48px; background: rgba(225, 29, 72, 0.08); border: 1px solid rgba(225, 29, 72, 0.25); border-radius: 10px;">
        <div style="font-size: 40px; margin-bottom: 12px;">🔍</div>
        <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">No Garage Record Found</h3>
        <p style="color: var(--grey); font-size: 14px; margin-bottom: 20px;">
          We couldn't find active garage records for "<strong><?= htmlspecialchars($searchQuery) ?></strong>". Please ensure your registered mobile number is entered correctly.
        </p>
        <a href="https://wa.me/919419100000?text=Hi%20The%20Drive%20Clinic,%20I%20need%20help%20locating%20my%20vehicle%20service%20record." target="_blank" class="btn btn-ghost-dark" style="color:#fff; border-color:rgba(255,255,255,0.2);">
          Contact Studio Concierge on WhatsApp →
        </a>
      </div>
    <?php elseif ($customer): ?>

      <!-- Client Profile Top Banner -->
      <div style="background: linear-gradient(135deg, rgba(225, 29, 72, 0.12) 0%, rgba(14, 18, 27, 0.95) 100%); border: 1px solid rgba(225, 29, 72, 0.3); border-radius: 12px; padding: 32px 36px; margin-bottom: 36px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
          <div style="font-size:11px; font-weight:800; letter-spacing:0.18em; color:var(--red); text-transform:uppercase; margin-bottom:4px;">VERIFIED CLIENT ACCOUNT</div>
          <h2 style="font-family:var(--font-display); font-size:36px; letter-spacing:0.04em; color:#fff; text-transform:uppercase; margin-bottom:6px;">
            <?= htmlspecialchars($customer['name']) ?>
          </h2>
          <div style="color:var(--text-2); font-size:14px;">
            Phone: <strong style="color:#fff;"><?= htmlspecialchars($customer['phone']) ?></strong>
            <?php if (!empty($customer['email'])): ?>
              &bull; Email: <strong style="color:#fff;"><?= htmlspecialchars($customer['email']) ?></strong>
            <?php endif; ?>
          </div>
        </div>

        <?php if ($membership && $membership['status'] === 'ACTIVE'): ?>
          <div style="background:#0F172A; border:1px solid #10B981; border-radius:8px; padding:16px 20px; text-align:right;">
            <div style="font-size:10px; font-weight:800; color:#10B981; letter-spacing:0.15em; text-transform:uppercase;">VIP PASS HOLDER</div>
            <div style="font-family:var(--font-display); font-size:20px; color:#fff; margin:2px 0;"><?= htmlspecialchars($membership['plan_name']) ?></div>
            <div style="font-size:13px; color:#94A3B8;">
              <strong style="color:#10B981; font-size:16px;"><?= $membership['washes_remaining'] ?></strong> / <?= $membership['washes_total'] ?> Washes Remaining
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Grid: Vehicles & Live Bay Status -->
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:24px; margin-bottom:44px;">
        
        <!-- Vehicles Registered -->
        <div style="background:#111522; border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:28px;">
          <h3 style="font-family:var(--font-display); font-size:24px; margin-bottom:16px; color:#fff; display:flex; align-items:center; gap:8px;">
            <span>🚗</span> REGISTERED VEHICLES
          </h3>
          <?php if (!empty($vehicles)): ?>
            <div style="display:flex; flex-direction:column; gap:12px;">
              <?php foreach ($vehicles as $v): ?>
                <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:6px; padding:14px 18px; display:flex; justify-content:space-between; align-items:center;">
                  <div>
                    <strong style="font-size:16px; color:#fff;"><?= htmlspecialchars(($v['brand'] ?? '') . ' ' . ($v['model'] ?? 'Vehicle')) ?></strong>
                    <div style="font-size:12px; color:var(--text-3); margin-top:2px;"><?= htmlspecialchars($v['color'] ?: 'Factory OEM Finish') ?></div>
                  </div>
                  <span style="font-family:monospace; background:#FEF08A; color:#713F12; padding:3px 8px; border-radius:4px; font-weight:800; font-size:12px;">
                    <?= htmlspecialchars($v['registration'] ?: 'Pending Reg') ?>
                  </span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p style="color:var(--text-3); font-size:13px;">No separate vehicle records registered.</p>
          <?php endif; ?>
        </div>

        <!-- Live Bay Service Progress -->
        <div style="background:#111522; border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:28px;">
          <h3 style="font-family:var(--font-display); font-size:24px; margin-bottom:16px; color:#fff; display:flex; align-items:center; gap:8px;">
            <span>🔧</span> ACTIVE BAY SERVICE TRACKER
          </h3>
          <?php if (!empty($jobCards)): 
            $latestJob = $jobCards[0];
          ?>
            <div style="background:rgba(225, 29, 72, 0.06); border:1px solid rgba(225, 29, 72, 0.2); border-radius:8px; padding:18px;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <span style="font-size:11px; font-weight:800; color:var(--red); letter-spacing:0.1em; text-transform:uppercase;">JOB CARD #<?= htmlspecialchars($latestJob['card_no']) ?></span>
                <span style="font-size:11px; font-weight:700; background:#10B981; color:#000; padding:2px 8px; border-radius:4px; text-transform:uppercase;"><?= htmlspecialchars($latestJob['status']) ?></span>
              </div>
              <div style="font-size:17px; font-weight:700; color:#fff; margin-bottom:4px;"><?= htmlspecialchars($latestJob['service_names']) ?></div>
              <div style="font-size:13px; color:var(--text-3); margin-bottom:12px;">Workshop Bay 0<?= $latestJob['bay_no'] ?> &bull; Lead Detailer: <?= htmlspecialchars($latestJob['assigned_tech'] ?: 'Studio Lead') ?></div>
              
              <!-- Progress Steps -->
              <div style="display:flex; gap:6px; margin-top:12px;">
                <div style="flex:1; height:4px; background:#10B981; border-radius:2px;"></div>
                <div style="flex:1; height:4px; background:#10B981; border-radius:2px;"></div>
                <div style="flex:1; height:4px; background:<?= in_array($latestJob['status'], ['IN_PROGRESS', 'COMPLETED']) ? '#10B981' : 'rgba(255,255,255,0.1)' ?>; border-radius:2px;"></div>
                <div style="flex:1; height:4px; background:<?= $latestJob['status'] === 'COMPLETED' ? '#10B981' : 'rgba(255,255,255,0.1)' ?>; border-radius:2px;"></div>
              </div>
              <div style="display:flex; justify-content:space-between; font-size:10px; color:var(--text-3); margin-top:6px;">
                <span>Decon</span>
                <span>Correction</span>
                <span>Application</span>
                <span>Ready</span>
              </div>
            </div>
          <?php else: ?>
            <div style="padding:24px; text-align:center; color:var(--text-3); font-size:13px;">
              No vehicle currently on the detailing bays. Ready for your next reservation!
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Invoices Section -->
      <div style="background:#111522; border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:32px; margin-bottom:44px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
          <div>
            <h3 style="font-family:var(--font-display); font-size:26px; color:#fff; margin-bottom:2px;">
              TAX INVOICES & STATEMENTS
            </h3>
            <p style="color:var(--text-3); font-size:13px;">Download and print GST tax invoices for warranty records or insurance reimbursement.</p>
          </div>
        </div>

        <?php if (!empty($invoices)): ?>
          <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:13px; text-align:left;">
              <thead>
                <tr style="border-bottom:1px solid rgba(255,255,255,0.12); color:var(--text-3); font-size:11px; text-transform:uppercase;">
                  <th style="padding:12px 14px;">Invoice #</th>
                  <th style="padding:12px 14px;">Date</th>
                  <th style="padding:12px 14px;">Vehicle</th>
                  <th style="padding:12px 14px;">Amount</th>
                  <th style="padding:12px 14px;">Status</th>
                  <th style="padding:12px 14px; text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($invoices as $inv): ?>
                  <tr style="border-bottom:1px solid rgba(255,255,255,0.06);">
                    <td style="padding:14px; font-weight:700; color:var(--red); font-family:monospace;"><?= htmlspecialchars($inv['invoice_no']) ?></td>
                    <td style="padding:14px; color:#94A3B8;"><?= date('d M Y', strtotime($inv['created_at'])) ?></td>
                    <td style="padding:14px;"><?= htmlspecialchars($inv['vehicle_model'] ?: 'Vehicle') ?></td>
                    <td style="padding:14px; font-weight:700; color:#fff;">₹<?= number_format((float)$inv['grand_total']) ?></td>
                    <td style="padding:14px;">
                      <?php if ($inv['payment_status'] === 'PAID'): ?>
                        <span style="background:rgba(16,185,129,0.15); color:#10B981; padding:3px 8px; border-radius:4px; font-size:11px; font-weight:700;">PAID (<?= htmlspecialchars($inv['payment_method'] ?: 'UPI') ?>)</span>
                      <?php else: ?>
                        <span style="background:rgba(245,158,11,0.15); color:#F59E0B; padding:3px 8px; border-radius:4px; font-size:11px; font-weight:700;">PENDING</span>
                      <?php endif; ?>
                    </td>
                    <td style="padding:14px; text-align:right;">
                      <a href="/crm/invoices/<?= $inv['id'] ?>/print" target="_blank" class="btn btn-red" style="padding:6px 12px; font-size:11px;">
                        🖨️ View / Print PDF
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p style="color:var(--text-3); font-size:13px; text-align:center; padding:24px;">No invoices generated yet for this account.</p>
        <?php endif; ?>
      </div>

      <!-- PPF & Ceramic Digital Warranty Certificates -->
      <div style="background:#111522; border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:32px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
          <div>
            <h3 style="font-family:var(--font-display); font-size:26px; color:#fff; margin-bottom:2px;">
              PPF & CERAMIC DIGITAL WARRANTY CERTIFICATES
            </h3>
            <p style="color:var(--text-3); font-size:13px;">Official manufacturer roll batch numbers, self-healing guarantees, and scheduled inspection dates.</p>
          </div>
        </div>

        <?php if (!empty($warranties)): ?>
          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:20px;">
            <?php foreach ($warranties as $w): ?>
              <div style="background:linear-gradient(135deg, rgba(197, 160, 89, 0.08) 0%, rgba(14, 18, 27, 0.95) 100%); border:1px solid rgba(197, 160, 89, 0.35); border-radius:8px; padding:22px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                  <div>
                    <span style="font-size:10px; font-weight:800; color:#C5A059; letter-spacing:0.15em;">CERTIFICATE #<?= htmlspecialchars($w['cert_no']) ?></span>
                    <h4 style="font-family:var(--font-display); font-size:22px; color:#fff; margin-top:2px;"><?= htmlspecialchars($w['product_brand'] ?: 'STEK DYNOshield PPF') ?></h4>
                  </div>
                  <span style="background:rgba(16,185,129,0.15); color:#10B981; font-weight:700; font-size:11px; padding:3px 8px; border-radius:4px;">ACTIVE</span>
                </div>
                <div style="font-size:13px; color:#CBD5E1; margin-bottom:12px;">
                  Vehicle: <strong><?= htmlspecialchars($w['vehicle_model'] ?: 'Car') ?> (<?= htmlspecialchars($w['vehicle_reg'] ?: 'JK 02') ?>)</strong><br>
                  Batch No: <strong style="font-family:monospace; color:#C5A059;"><?= htmlspecialchars($w['roll_batch_no'] ?: 'STK-2026') ?></strong><br>
                  Term: <strong><?= $w['warranty_years'] ?> Years Coverage</strong> &bull; Scope: <?= htmlspecialchars($w['coverage_scope'] ?: 'Full Body') ?>
                </div>
                <div style="border-top:1px solid rgba(197, 160, 89, 0.2); padding-top:12px; display:flex; justify-content:space-between; align-items:center;">
                  <span style="font-size:11px; color:#94A3B8;">Next Inspection: <?= $w['next_inspection_date'] ? date('d M Y', strtotime($w['next_inspection_date'])) : '6 Months' ?></span>
                  <a href="/crm/warranty/<?= $w['id'] ?>/print" target="_blank" class="btn" style="background:#C5A059; color:#000; font-weight:800; padding:6px 12px; font-size:11px;">
                    📜 View Certificate
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p style="color:var(--text-3); font-size:13px; text-align:center; padding:24px;">No PPF warranty certificates registered for this account.</p>
        <?php endif; ?>
      </div>

    <?php endif; ?>

  </div>
</div>
