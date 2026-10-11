<?php
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
?>
<div class="crm-customers-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">WORKSPACE &bull; CLIENT RELATIONS</div>
      <h1 class="crm-page-heading">Customers & Vehicle Garage</h1>
      <p class="crm-page-sub">Central directory of vehicle owners, registered cars & bikes, visit history, and lifetime value.</p>
    </div>
    <div class="crm-page-actions">
      <button class="crm-btn crm-btn-primary" onclick="openNewBookingModal()">+ Add Customer</button>
    </div>
  </div>

  <div class="crm-card">
    <div class="crm-table-container">
      <table class="crm-table">
        <thead>
          <tr>
            <th>Customer Profile</th>
            <th>Contact & WhatsApp</th>
            <th>Email</th>
            <th>Registered Vehicles</th>
            <th>Total Visits</th>
            <th>Lifetime Revenue</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($customers)): ?>
            <?php foreach ($customers as $c): ?>
            <tr>
              <td>
                <div class="crm-avatar-flex">
                  <div class="crm-customer-circle"><?= strtoupper(substr($c['name'], 0, 2)) ?></div>
                  <div>
                    <strong><?= htmlspecialchars($c['name']) ?></strong>
                    <div class="crm-text-muted" style="font-size:11px;">Member since <?= date('M Y', strtotime($c['created_at'])) ?></div>
                  </div>
                </div>
              </td>
              <td>
                <strong><?= htmlspecialchars($c['phone']) ?></strong>
                <div class="crm-whatsapp-indicator">
                  <a href="https://wa.me/<?= preg_replace('/\D/', '', $c['whatsapp'] ?: $c['phone']) ?>" target="_blank" class="crm-wa-link">
                    💬 Chat WhatsApp
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
                <strong class="crm-amount-val">₹<?= number_format((float)$c['total_spent']) ?></strong>
              </td>
              <td>
                <button class="crm-btn crm-btn-xs crm-btn-ghost" onclick="alert('Viewing garage records for <?= addslashes($c['name']) ?>')">
                  View Garage
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
