<?php
// THE DRIVE CLINIC — Digital Warranty & PPF Certification
$warranties = Database::query("
  SELECT w.*, c.name as customer_name, c.phone as customer_phone,
         v.brand, v.model, v.registration
  FROM warranty_certificates w
  LEFT JOIN customers c ON w.customer_id = c.id
  LEFT JOIN vehicles v ON w.vehicle_id = v.id
  ORDER BY w.created_at DESC
");
?>
<div class="crm-warranty-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">BUSINESS &bull; WARRANTY & QUALITY</div>
      <h1 class="crm-page-heading">Digital Warranty & PPF Roll Registry</h1>
      <p class="crm-page-sub">Official warranties for STEK PPF installations and Gtechniq Ceramic coatings with batch verification.</p>
    </div>
    <div class="crm-page-actions">
      <button class="crm-btn crm-btn-primary" onclick="alert('Generate new digital warranty certificate')">+ Issue Certificate</button>
    </div>
  </div>

  <div class="crm-card">
    <div class="crm-table-container">
      <table class="crm-table">
        <thead>
          <tr>
            <th>Certificate No.</th>
            <th>Customer & Vehicle</th>
            <th>Product & Brand</th>
            <th>Roll / Batch #</th>
            <th>Warranty Period</th>
            <th>Installation Date</th>
            <th>Next Annual Inspection</th>
            <th>Status</th>
            <th>Digital Pass</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($warranties as $w): ?>
          <tr>
            <td><strong class="crm-ref-code"><?= htmlspecialchars($w['cert_no']) ?></strong></td>
            <td>
              <strong><?= htmlspecialchars($w['customer_name'] ?? 'Client') ?></strong>
              <div class="crm-text-muted" style="font-size:11px;"><?= htmlspecialchars(($w['brand'] ?? '') . ' ' . ($w['model'] ?? '')) ?> (<?= htmlspecialchars($w['registration'] ?? '') ?>)</div>
            </td>
            <td>
              <strong><?= htmlspecialchars($w['product_brand']) ?></strong>
              <div class="crm-text-muted" style="font-size:11px;"><?= htmlspecialchars($w['service_slug']) ?></div>
            </td>
            <td>
              <span class="crm-sku-badge"><?= htmlspecialchars($w['roll_batch_no']) ?></span>
            </td>
            <td><strong><?= $w['warranty_years'] ?> Years Official</strong></td>
            <td><?= date('d M Y', strtotime($w['install_date'])) ?></td>
            <td>
              <strong class="crm-text-amber"><?= date('d M Y', strtotime($w['next_inspection_date'])) ?></strong>
            </td>
            <td>
              <span class="crm-pill crm-pill-success"><?= htmlspecialchars($w['status']) ?></span>
            </td>
            <td>
              <button class="crm-btn crm-btn-xs crm-btn-ghost" onclick="alert('Viewing digital QR certificate: <?= htmlspecialchars($w['cert_no']) ?>')">
                🛡️ View QR Pass
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
