<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Digital Warranty & PPF Certification
$warranties = Database::query("
  SELECT w.*, 
         COALESCE(w.customer_name, c.name, 'Client') as cust_display_name,
         COALESCE(w.customer_phone, c.phone, '—') as cust_display_phone,
         COALESCE(w.vehicle_model, v.model, 'Vehicle') as veh_display_model,
         COALESCE(w.vehicle_reg, v.registration, 'Pending Reg') as veh_display_reg
  FROM warranty_certificates w
  LEFT JOIN customers c ON w.customer_id = c.id
  LEFT JOIN vehicles v ON w.vehicle_id = v.id
  ORDER BY w.created_at DESC
");

$customers = Database::query("SELECT id, name, phone FROM customers ORDER BY name ASC");
$activeCount = count(array_filter($warranties, fn($w) => $w['status'] === 'ACTIVE'));
?>
<div class="crm-warranty-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">BUSINESS &bull; WARRANTY & QUALITY</div>
      <h1 class="crm-page-heading">Digital PPF & Ceramic Warranty Registry</h1>
      <p class="crm-page-sub">Official manufacturer warranties for STEK PPF self-healing installations and Gtechniq Ceramic coatings with roll batch verification, digital certificates, and WhatsApp delivery.</p>
    </div>
    <div class="crm-page-actions">
      <button class="crm-btn crm-btn-primary" onclick="openModal('modalIssueWarranty')">+ Issue Warranty Certificate</button>
    </div>
  </div>

  <!-- Registry KPI Cards -->
  <div class="crm-kpi-grid">
    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Active Warranties in Registry</span>
        <span class="crm-kpi-icon" style="color:#10b981;">🛡️</span>
      </div>
      <div class="crm-kpi-value" style="color:#10b981;"><?= $activeCount ?></div>
      <div class="crm-kpi-change positive">Protected Vehicles on Jammu Roads</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">TPU Self-Healing Films</span>
        <span class="crm-kpi-icon" style="color:#38bdf8;">✨</span>
      </div>
      <div class="crm-kpi-value"><?= count(array_filter($warranties, fn($w) => stripos($w['product_brand'] ?? '', 'ppf') !== false || stripos($w['product_brand'] ?? '', 'stek') !== false || stripos($w['product_brand'] ?? '', 'garware') !== false)) ?></div>
      <div class="crm-kpi-change positive">5, 7 & 10 Year Guarantees</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Inspection Check Schedule</span>
        <span class="crm-kpi-icon" style="color:#f59e0b;">🔍</span>
      </div>
      <div class="crm-kpi-value" style="color:#f59e0b;">6 Months</div>
      <div class="crm-kpi-change neutral">Complimentary edge wrap & top-up check</div>
    </div>
  </div>

  <!-- Warranties Table -->
  <div class="crm-card" style="margin-top:24px;">
    <div class="crm-card-header" style="border-bottom:1px solid var(--crm-border); padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
      <h2 class="crm-card-title">Enrolled Warranty Certificates</h2>
      <input type="text" id="warrantySearch" placeholder="Search customer, batch #, or plate..." class="crm-input" style="width:240px; padding:6px 12px; font-size:12px;" oninput="filterWarrantyTable()">
    </div>

    <div class="crm-table-container">
      <table class="crm-table" id="warrantyTable">
        <thead>
          <tr>
            <th>Certificate No.</th>
            <th>Customer & Vehicle</th>
            <th>Product & Manufacturer</th>
            <th>Roll / Batch #</th>
            <th>Warranty Term</th>
            <th>Installation Date</th>
            <th>Next 6-Mo Inspection</th>
            <th>Status</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($warranties)): ?>
            <?php foreach ($warranties as $w): 
              $cleanP = preg_replace('/\D/', '', (string)$w['cust_display_phone']);
              if (strlen($cleanP) === 10) $cleanP = '91' . $cleanP;
              $siteUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
              $certUrl = "{$siteUrl}/crm/warranty/{$w['id']}/print";
              $waMsg = urlencode("🛡️ *THE DRIVE CLINIC — OFFICIAL PPF WARRANTY CERTIFICATE* 🛡️\n\nDear {$w['cust_display_name']},\nYour {$w['veh_display_model']} ({$w['veh_display_reg']}) warranty certificate #{$w['cert_no']} for {$w['product_brand']} is active.\n\n📜 View Digital Certificate: {$certUrl}\n\nThank you for choosing The Drive Clinic Jammu!");
            ?>
            <tr>
              <td><strong class="crm-ref-code"><?= htmlspecialchars($w['cert_no']) ?></strong></td>
              <td>
                <strong><?= htmlspecialchars($w['cust_display_name']) ?></strong>
                <div class="crm-text-muted" style="font-size:11px;">
                  <?= htmlspecialchars($w['veh_display_model']) ?> 
                  <span class="crm-plate-tag"><?= htmlspecialchars($w['veh_display_reg']) ?></span>
                </div>
              </td>
              <td>
                <strong><?= htmlspecialchars($w['product_brand']) ?></strong>
                <div class="crm-text-muted" style="font-size:11px;"><?= htmlspecialchars($w['coverage_scope'] ?: 'Full Exterior') ?></div>
              </td>
              <td>
                <span class="crm-sku-badge" style="font-family:monospace;"><?= htmlspecialchars($w['roll_batch_no'] ?: 'STK-2026') ?></span>
              </td>
              <td><strong><?= $w['warranty_years'] ?> Years</strong></td>
              <td><?= $w['install_date'] ? date('d M Y', strtotime($w['install_date'])) : '—' ?></td>
              <td>
                <strong style="color:#f59e0b;"><?= $w['next_inspection_date'] ? date('d M Y', strtotime($w['next_inspection_date'])) : '6 Months' ?></strong>
              </td>
              <td>
                <span class="crm-pill crm-pill-success"><?= htmlspecialchars($w['status'] ?: 'ACTIVE') ?></span>
              </td>
              <td style="text-align:right;">
                <div class="crm-action-group" style="justify-content:flex-end;">
                  <a href="/crm/warranty/<?= $w['id'] ?>/print" target="_blank" class="crm-btn crm-btn-xs crm-btn-ghost">
                    📜 View & Print
                  </a>
                  <a href="https://wa.me/<?= $cleanP ?>?text=<?= $waMsg ?>" target="_blank" rel="noopener" class="crm-btn crm-btn-xs" style="background:#25D366; color:#000; font-weight:700;">
                    💬 WhatsApp
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" style="text-align:center; padding:44px 20px; color:#888888;">
                <div style="font-size:32px; margin-bottom:10px;">🛡️</div>
                <strong style="color:#ffffff; font-size:14px;">No Digital Warranties Issued Yet</strong>
                <p style="font-size:12px; margin:6px 0 0; color:#666666;">Issue official warranties for STEK PPF and ceramic coatings using the button above.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal: Issue New Warranty Certificate -->
<div class="crm-modal-backdrop" id="modalIssueWarranty">
  <div class="crm-modal crm-modal-md">
    <div class="crm-modal-header">
      <div class="crm-modal-title-group">
        <h3 class="crm-modal-title">Issue Digital Warranty Certificate</h3>
        <p class="crm-modal-sub">Register vehicle PPF or ceramic installation into studio warranty registry.</p>
      </div>
      <button class="crm-modal-close" onclick="closeModal('modalIssueWarranty')">&times;</button>
    </div>
    <form id="formIssueWarranty" onsubmit="handleIssueWarranty(event)">
      <div class="crm-modal-body">
        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Customer Name *</label>
            <input type="text" name="customer_name" class="crm-input" placeholder="e.g. Vikram Sharma" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Customer Mobile Phone *</label>
            <input type="tel" name="customer_phone" class="crm-input" placeholder="+91 94191 XXXXX" required>
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Vehicle Make & Model *</label>
            <input type="text" name="vehicle_model" class="crm-input" placeholder="e.g. BMW 320d M-Sport" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Registration Number *</label>
            <input type="text" name="vehicle_reg" class="crm-input" placeholder="e.g. JK 02 CQ 7788" required style="text-transform:uppercase;">
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Product Brand & Grade *</label>
            <select name="product_brand" class="crm-select" required>
              <option value="STEK DYNOshield Gloss PPF">STEK DYNOshield Gloss TPU PPF (10 Yrs)</option>
              <option value="STEK DYNOmatte PPF">STEK DYNOmatte Satin PPF (10 Yrs)</option>
              <option value="Garware Premium Plus PPF">Garware Premium Plus PPF (5 Yrs)</option>
              <option value="SunTek Ultra PPF">SunTek Ultra Self-Healing Film (7 Yrs)</option>
              <option value="3M Scotchgard Pro Series">3M Scotchgard Pro Series PPF (7 Yrs)</option>
              <option value="Avery Dennison Supreme PPF">Avery Dennison Supreme Defense (10 Yrs)</option>
              <option value="Gtechniq Crystal Serum Ultra 9H">Gtechniq Crystal Serum Ultra 9H Ceramic (5 Yrs)</option>
              <option value="CarPro CQuartz Professional">CarPro CQuartz Professional Ceramic (3 Yrs)</option>
            </select>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Manufacturer Roll / Batch No. *</label>
            <input type="text" name="roll_batch_no" class="crm-input" placeholder="e.g. STK-2026-B849" required style="font-family:monospace;">
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Warranty Duration (Years) *</label>
            <select name="warranty_years" class="crm-select" required>
              <option value="3">3 Years</option>
              <option value="5" selected>5 Years</option>
              <option value="7">7 Years</option>
              <option value="10">10 Years</option>
            </select>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Coverage Scope *</label>
            <select name="coverage_scope" class="crm-select" required>
              <option value="Full Body Wrap (Edge-Wrapped)">Full Body Wrap (Edge-Wrapped)</option>
              <option value="High-Impact Package (Bumper, Bonnet, Fenders, ORVMs)">High-Impact Package (Frontal)</option>
              <option value="Bonnet + Bumper + Headlights">Bonnet + Bumper + Headlights</option>
              <option value="Full Exterior Ceramic Coating (Paint + Wheels + Glass)">Full Exterior Ceramic (Paint, Wheels, Glass)</option>
              <option value="Custom Panel Package">Custom Panel Package</option>
            </select>
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Installation Date *</label>
            <input type="date" name="install_date" class="crm-input" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">First Mandatory Inspection (6 Months) *</label>
            <input type="date" name="next_inspection_date" class="crm-input" value="<?= date('Y-m-d', strtotime('+6 months')) ?>" required>
          </div>
        </div>

        <div class="crm-form-group">
          <label class="crm-label">Application Notes / Applicator Details</label>
          <textarea name="notes" class="crm-textarea" placeholder="e.g. Installed in Bay 01 dust-free cleanroom by Aman Verma. Zero stretch lines."></textarea>
        </div>
      </div>
      <div class="crm-modal-footer">
        <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalIssueWarranty')">Cancel</button>
        <button type="submit" class="crm-btn crm-btn-primary">Register & Issue Certificate</button>
      </div>
    </form>
  </div>
</div>

<script>
function filterWarrantyTable() {
  const query = document.getElementById('warrantySearch').value.toLowerCase();
  const rows = document.querySelectorAll('#warrantyTable tbody tr');
  rows.forEach(r => {
    r.style.display = r.textContent.toLowerCase().includes(query) ? '' : 'none';
  });
}

async function handleIssueWarranty(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Enrolling in Registry...';

  try {
    const res = await fetch('/crm/api/warranty/create', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
      showToast('Warranty Certificate ' + (data.cert_no || '') + ' issued successfully!', 'success');
      closeModal('modalIssueWarranty');
      setTimeout(() => location.reload(), 600);
    } else {
      showToast(data.error || 'Failed to issue certificate', 'danger');
    }
  } catch (err) {
    showToast('Network error registering warranty', 'danger');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Register & Issue Certificate';
  }
}
</script>
