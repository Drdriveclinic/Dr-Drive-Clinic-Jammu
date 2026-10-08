<?php
// THE DRIVE CLINIC — CRM Services & Offers
$services = Database::query("SELECT * FROM services ORDER BY sort_order ASC");
?>
<div class="crm-services-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">BUSINESS &bull; SERVICE CATALOG</div>
      <h1 class="crm-page-heading">Services & Studio Packages</h1>
      <p class="crm-page-sub">Manage detailing packages, PPF warranties, pricing tiers, and bay duration.</p>
    </div>
  </div>

  <div class="crm-card">
    <div class="crm-table-container">
      <table class="crm-table">
        <thead>
          <tr>
            <th>Service Name</th>
            <th>Category</th>
            <th>Supported Vehicles</th>
            <th>Estimated Duration</th>
            <th>Active Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($services as $s): ?>
          <tr>
            <td>
              <strong><?= htmlspecialchars($s['name']) ?></strong>
              <div class="crm-text-muted" style="font-size:11px;"><?= htmlspecialchars($s['short_desc']) ?></div>
            </td>
            <td><span class="crm-cat-tag"><?= htmlspecialchars($s['category']) ?></span></td>
            <td><?= htmlspecialchars($s['vehicle_types']) ?></td>
            <td><strong><?= htmlspecialchars($s['duration_note'] ?: '2-4 Hours') ?></strong></td>
            <td><span class="crm-pill crm-pill-success">Live on Website</span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
