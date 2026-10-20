<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Customers & Vehicle Garage Directory
$customers = Database::query("
  SELECT c.*, 
         COUNT(DISTINCT v.id) as vehicle_count,
         COUNT(DISTINCT j.id) as job_count,
         COALESCE(SUM(i.grand_total), 0) as total_spent
  FROM customers c
  LEFT JOIN vehicles v ON c.id = v.customer_id
  LEFT JOIN job_cards j ON c.id = j.customer_id
  LEFT JOIN invoices i ON c.id = i.customer_id
  GROUP BY c.id
  ORDER BY c.created_at DESC
");

// Preload vehicles, job cards, invoices and warranties indexed by customer_id or phone
$allVehicles = Database::query("SELECT * FROM vehicles ORDER BY id DESC");
$allJobCards = Database::query("SELECT * FROM job_cards ORDER BY created_at DESC");
$allInvoices = Database::query("SELECT * FROM invoices ORDER BY created_at DESC");
$allWarranties = Database::query("SELECT * FROM warranty_certificates ORDER BY created_at DESC");
$allMemberships = Database::query("SELECT * FROM memberships WHERE status = 'ACTIVE'");

$vehiclesByCust = [];
foreach ($allVehicles as $v) {
    $vehiclesByCust[$v['customer_id']][] = $v;
}

$jobsByCust = [];
foreach ($allJobCards as $j) {
    $jobsByCust[$j['customer_id']][] = $j;
}

$invoicesByCust = [];
foreach ($allInvoices as $inv) {
    $cid = $inv['customer_id'];
    $phone = $inv['customer_phone'];
    if ($cid) $invoicesByCust['cid_' . $cid][] = $inv;
    if ($phone) $invoicesByCust['ph_' . preg_replace('/\D/', '', $phone)][] = $inv;
}

$warrantiesByCust = [];
foreach ($allWarranties as $w) {
    $cid = $w['customer_id'];
    $phone = $w['customer_phone'];
    if ($cid) $warrantiesByCust['cid_' . $cid][] = $w;
    if ($phone) $warrantiesByCust['ph_' . preg_replace('/\D/', '', $phone)][] = $w;
}

$membershipsByPhone = [];
foreach ($allMemberships as $m) {
    $membershipsByPhone[preg_replace('/\D/', '', $m['phone'])] = $m;
}

// Compute client lifetime aggregates
$totalClients = count($customers);
$totalVehicles = count($allVehicles);
$totalRevenue = 0.0;
foreach ($customers as $c) {
    $totalRevenue += (float)$c['total_spent'];
}
$avgSpend = $totalClients > 0 ? round($totalRevenue / $totalClients) : 0;
?>
<div class="crm-customers-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">WORKSPACE &bull; CLIENT RELATIONS</div>
      <h1 class="crm-page-heading">Customer Garage & Relationship Panel</h1>
      <p class="crm-page-sub">Central customer directory, registered vehicle fleet, full service history, tax invoices, and digital PPF warranty certificates.</p>
    </div>
    <div class="crm-page-actions">
      <button class="crm-btn crm-btn-primary" onclick="openNewBookingModal()">+ Register Customer</button>
    </div>
  </div>

  <!-- Client Metrics -->
  <div class="crm-kpi-grid">
    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Total Registered Clients</span>
        <span class="crm-kpi-icon" style="color:#38bdf8;">👤</span>
      </div>
      <div class="crm-kpi-value"><?= $totalClients ?></div>
      <div class="crm-kpi-change positive">Verified vehicle owners</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Total Garage Fleet</span>
        <span class="crm-kpi-icon" style="color:#10b981;">🚗</span>
      </div>
      <div class="crm-kpi-value" style="color:#10b981;"><?= $totalVehicles ?> Vehicles</div>
      <div class="crm-kpi-change positive">Cars, SUVs & Superbikes</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Total Client Lifetime Value</span>
        <span class="crm-kpi-icon" style="color:#f59e0b;">💎</span>
      </div>
      <div class="crm-kpi-value" style="color:#f59e0b;">₹<?= number_format($totalRevenue) ?></div>
      <div class="crm-kpi-change positive">Invoiced Detailing & Protection</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Average Revenue Per Client</span>
        <span class="crm-kpi-icon" style="color:#e11d48;">📊</span>
      </div>
      <div class="crm-kpi-value">₹<?= number_format($avgSpend) ?></div>
      <div class="crm-kpi-change neutral">High-ticket detailing average</div>
    </div>
  </div>

  <!-- Customers Directory Card -->
  <div class="crm-card" style="margin-top:24px;">
    <div class="crm-card-header" style="border-bottom:1px solid var(--crm-border); padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
      <h2 class="crm-card-title">All Customer Profiles</h2>
      <input type="text" id="custSearchInput" placeholder="Search name, phone, or email..." class="crm-input" style="width:240px; padding:6px 12px; font-size:12px;" oninput="filterCustomerTable()">
    </div>

    <div class="crm-table-container">
      <table class="crm-table" id="customersTable">
        <thead>
          <tr>
            <th>Customer Profile</th>
            <th>Contact & WhatsApp</th>
            <th>Email</th>
            <th>Registered Vehicles</th>
            <th>Total Visits</th>
            <th>Lifetime Revenue</th>
            <th style="text-align:right;">Garage Panel</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($customers)): ?>
            <?php foreach ($customers as $c): 
              $cId = (int)$c['id'];
              $cleanP = preg_replace('/\D/', '', (string)($c['whatsapp'] ?: $c['phone']));
              if (strlen($cleanP) === 10) $cleanP = '91' . $cleanP;

              $custVehicles = $vehiclesByCust[$cId] ?? [];
              $custJobs = $jobsByCust[$cId] ?? [];
              $custInvoices = array_merge(
                  $invoicesByCust['cid_' . $cId] ?? [],
                  $invoicesByCust['ph_' . $cleanP] ?? []
              );
              $custWarranties = array_merge(
                  $warrantiesByCust['cid_' . $cId] ?? [],
                  $warrantiesByCust['ph_' . $cleanP] ?? []
              );
              $custMem = $membershipsByPhone[$cleanP] ?? null;

              $profileData = [
                  'name' => $c['name'],
                  'phone' => $c['phone'],
                  'email' => $c['email'] ?: '—',
                  'created_at' => date('d M Y', strtotime($c['created_at'])),
                  'total_spent' => number_format((float)$c['total_spent']),
                  'vehicles' => $custVehicles,
                  'jobs' => $custJobs,
                  'invoices' => $custInvoices,
                  'warranties' => $custWarranties,
                  'membership' => $custMem
              ];
            ?>
            <tr>
              <td>
                <div class="crm-avatar-flex" style="cursor:pointer;" onclick="openCustomerDrawer(<?= htmlspecialchars(json_encode($profileData)) ?>)">
                  <div class="crm-customer-circle"><?= strtoupper(substr($c['name'], 0, 2)) ?></div>
                  <div>
                    <strong style="color:#fff;"><?= htmlspecialchars($c['name']) ?></strong>
                    <div class="crm-text-muted" style="font-size:11px;">Member since <?= date('M Y', strtotime($c['created_at'])) ?></div>
                  </div>
                </div>
              </td>
              <td>
                <strong><?= htmlspecialchars($c['phone']) ?></strong>
                <div class="crm-whatsapp-indicator">
                  <a href="https://wa.me/<?= $cleanP ?>" target="_blank" class="crm-wa-link">
                    💬 WhatsApp
                  </a>
                </div>
              </td>
              <td>
                <span><?= htmlspecialchars($c['email'] ?: '—') ?></span>
              </td>
              <td>
                <span class="crm-pill crm-pill-info"><?= $c['vehicle_count'] ?> <?= $c['vehicle_count'] == 1 ? 'Vehicle' : 'Vehicles' ?></span>
              </td>
              <td>
                <strong><?= $c['job_count'] ?> Services</strong>
              </td>
              <td>
                <strong class="crm-amount-val" style="color:#10b981;">₹<?= number_format((float)$c['total_spent']) ?></strong>
              </td>
              <td style="text-align:right;">
                <button class="crm-btn crm-btn-xs crm-btn-primary" onclick="openCustomerDrawer(<?= htmlspecialchars(json_encode($profileData)) ?>)">
                  🏎️ View Garage
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" style="text-align:center; padding:44px 20px; color:#888888;">
                <div style="font-size:32px; margin-bottom:10px;">👤</div>
                <strong style="color:#ffffff; font-size:14px;">No Customer Records Yet</strong>
                <p style="font-size:12px; margin:6px 0 0; color:#666666;">Customer profiles are created automatically as appointments and job cards are logged.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal: Comprehensive Customer Garage Drawer -->
<div class="crm-modal-backdrop" id="modalCustomerDrawer">
  <div class="crm-modal crm-modal-lg" style="max-width:860px;">
    <div class="crm-modal-header">
      <div class="crm-modal-title-group">
        <h3 class="crm-modal-title" id="drawerCustName">Client Garage & Relationship Record</h3>
        <p class="crm-modal-sub" id="drawerCustSub">Detailed vehicle specifications, billing invoices, and warranty registry.</p>
      </div>
      <button class="crm-modal-close" onclick="closeModal('modalCustomerDrawer')">&times;</button>
    </div>
    <div class="crm-modal-body" id="drawerBody">
      <!-- Injected dynamically -->
    </div>
    <div class="crm-modal-footer">
      <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalCustomerDrawer')">Close</button>
      <a href="#" id="drawerWaBtn" target="_blank" class="crm-btn" style="background:#25D366; color:#000; font-weight:800;">
        💬 Chat on WhatsApp
      </a>
    </div>
  </div>
</div>

<script>
function filterCustomerTable() {
  const query = document.getElementById('custSearchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#customersTable tbody tr');
  rows.forEach(r => {
    r.style.display = r.textContent.toLowerCase().includes(query) ? '' : 'none';
  });
}

function openCustomerDrawer(data) {
  document.getElementById('drawerCustName').textContent = data.name + ' — Garage Dossier';
  document.getElementById('drawerCustSub').textContent = 'Phone: ' + data.phone + ' • Member since ' + data.created_at + ' • Lifetime Spend: ₹' + data.total_spent;

  const cleanP = data.phone.replace(/\D/g, '');
  document.getElementById('drawerWaBtn').href = 'https://wa.me/' + (cleanP.length === 10 ? '91' + cleanP : cleanP);

  let vehiclesHtml = '';
  if (data.vehicles && data.vehicles.length > 0) {
    data.vehicles.forEach(v => {
      vehiclesHtml += `
        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:6px; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <strong style="color:#fff; font-size:15px;">${v.brand || ''} ${v.model || 'Vehicle'}</strong>
            <div style="font-size:12px; color:#94A3B8;">${v.color || 'OEM Finish'} &bull; Type: ${v.type || 'Car'}</div>
          </div>
          <span class="crm-plate-tag">${v.registration || 'Pending Reg'}</span>
        </div>
      `;
    });
  } else {
    vehiclesHtml = '<div style="color:#888; font-size:12px; padding:12px;">No separate garage vehicles registered.</div>';
  }

  let invoicesHtml = '';
  if (data.invoices && data.invoices.length > 0) {
    data.invoices.forEach(inv => {
      invoicesHtml += `
        <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 12px; border-bottom:1px solid rgba(255,255,255,0.06); font-size:12px;">
          <div>
            <strong style="color:var(--red); font-family:monospace;">${inv.invoice_no}</strong>
            <span style="color:#94A3B8; margin-left:8px;">${inv.created_at ? inv.created_at.slice(0, 10) : ''}</span>
          </div>
          <div>
            <strong style="color:#fff; margin-right:12px;">₹${(parseFloat(inv.grand_total) || 0).toLocaleString()}</strong>
            <span class="crm-pill ${inv.payment_status === 'PAID' ? 'crm-pill-success' : 'crm-pill-warning'}">${inv.payment_status}</span>
            <a href="/crm/invoices/${inv.id}/print" target="_blank" class="crm-btn crm-btn-xs crm-btn-ghost" style="margin-left:8px;">🖨️ PDF</a>
          </div>
        </div>
      `;
    });
  } else {
    invoicesHtml = '<div style="color:#888; font-size:12px; padding:12px;">No billing invoices logged yet.</div>';
  }

  let warrantiesHtml = '';
  if (data.warranties && data.warranties.length > 0) {
    data.warranties.forEach(w => {
      warrantiesHtml += `
        <div style="background:rgba(197, 160, 89, 0.08); border:1px solid rgba(197, 160, 89, 0.3); border-radius:6px; padding:12px; margin-bottom:8px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <span style="font-size:10px; font-weight:800; color:#C5A059;">CERT #${w.cert_no}</span>
            <div style="font-weight:700; color:#fff;">${w.product_brand || 'PPF Armor'} (${w.warranty_years || 5} Years)</div>
            <div style="font-size:11px; color:#94A3B8;">Batch: ${w.roll_batch_no || 'STK'} &bull; Insp: ${w.next_inspection_date || '6-Mo'}</div>
          </div>
          <a href="/crm/warranty/${w.id}/print" target="_blank" class="crm-btn crm-btn-xs" style="background:#C5A059; color:#000; font-weight:800;">
            📜 Certificate
          </a>
        </div>
      `;
    });
  } else {
    warrantiesHtml = '<div style="color:#888; font-size:12px; padding:12px;">No PPF warranty certificates registered.</div>';
  }

  let vipHtml = '';
  if (data.membership) {
    vipHtml = `
      <div style="background:rgba(16,185,129,0.1); border:1px solid #10b981; border-radius:6px; padding:12px 16px; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center;">
        <div>
          <span style="font-size:10px; font-weight:800; color:#10b981;">ACTIVE VIP MEMBER</span>
          <div style="font-size:15px; font-weight:700; color:#fff;">${data.membership.plan_name}</div>
        </div>
        <div style="text-align:right;">
          <strong style="color:#10b981; font-size:16px;">${data.membership.washes_remaining}</strong> / ${data.membership.washes_total} Washes Remaining
          <div style="font-size:11px; color:#94A3B8;">Expires: ${data.membership.expiry_date}</div>
        </div>
      </div>
    `;
  }

  const html = `
    ${vipHtml}
    
    <!-- Registered Vehicles -->
    <div style="margin-bottom:20px;">
      <span style="font-size:11px; font-weight:800; color:var(--red); letter-spacing:0.12em; text-transform:uppercase;">REGISTERED GARAGE VEHICLES:</span>
      <div style="display:flex; flex-direction:column; gap:8px; margin-top:8px;">
        ${vehiclesHtml}
      </div>
    </div>

    <!-- Billing Invoices -->
    <div style="margin-bottom:20px;">
      <span style="font-size:11px; font-weight:800; color:var(--red); letter-spacing:0.12em; text-transform:uppercase;">INVOICES & RECEIPTS:</span>
      <div style="background:#0F1420; border-radius:6px; margin-top:8px; overflow:hidden;">
        ${invoicesHtml}
      </div>
    </div>

    <!-- Warranties -->
    <div>
      <span style="font-size:11px; font-weight:800; color:#C5A059; letter-spacing:0.12em; text-transform:uppercase;">DIGITAL PPF / CERAMIC WARRANTIES:</span>
      <div style="margin-top:8px;">
        ${warrantiesHtml}
      </div>
    </div>
  `;

  document.getElementById('drawerBody').innerHTML = html;
  openModal('modalCustomerDrawer');
}
</script>
