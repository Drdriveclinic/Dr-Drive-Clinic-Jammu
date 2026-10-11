<?php
// THE DRIVE CLINIC — Bookings Management
$appointments = Database::query("
  SELECT a.*, c.name as customer_name, c.phone as customer_phone,
         v.brand, v.model, v.registration, s.name as service_name
  FROM appointments a
  LEFT JOIN customers c ON a.customer_id = c.id
  LEFT JOIN vehicles v ON a.vehicle_id = v.id
  LEFT JOIN services s ON a.service_id = s.id
  ORDER BY a.date DESC, a.time ASC
");
?>
<div class="crm-bookings-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">WORKSPACE &bull; SCHEDULER</div>
      <h1 class="crm-page-heading">Studio Appointments & Bookings</h1>
      <p class="crm-page-sub">Manage customer reservations, schedule detailing slots, and convert to bay job cards.</p>
    </div>
    <div class="crm-page-actions">
      <button class="crm-btn crm-btn-primary" onclick="openNewBookingModal()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        <span>+ Create Booking</span>
      </button>
    </div>
  </div>

  <div class="crm-card">
    <div class="crm-table-container">
      <table class="crm-table">
        <thead>
          <tr>
            <th>Booking Ref</th>
            <th>Date & Time</th>
            <th>Customer</th>
            <th>Vehicle</th>
            <th>Service Requested</th>
            <th>Status</th>
            <th>Source</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($appointments)): ?>
            <?php foreach ($appointments as $apt): ?>
            <tr>
              <td><strong class="crm-ref-code"><?= htmlspecialchars($apt['ref']) ?></strong></td>
              <td>
                <div class="crm-time-stamp">
                  <strong><?= date('d M Y', strtotime($apt['date'])) ?></strong>
                  <span><?= htmlspecialchars($apt['time']) ?></span>
                </div>
              </td>
              <td>
                <strong><?= htmlspecialchars($apt['customer_name'] ?? 'Walk-in') ?></strong>
                <div class="crm-text-muted"><?= htmlspecialchars($apt['customer_phone'] ?? '—') ?></div>
              </td>
              <td>
                <strong><?= htmlspecialchars(($apt['brand'] ?? '') . ' ' . ($apt['model'] ?? 'Car')) ?></strong>
                <div class="crm-plate-tag"><?= htmlspecialchars($apt['registration'] ?: 'JK 02 XX 0000') ?></div>
              </td>
              <td>
                <span class="crm-service-tag"><?= htmlspecialchars($apt['service_name'] ?? 'Custom Detailing') ?></span>
              </td>
              <td>
                <?php if ($apt['status'] === 'CONFIRMED'): ?>
                  <span class="crm-pill crm-pill-confirmed">Confirmed</span>
                <?php elseif ($apt['status'] === 'IN_PROGRESS'): ?>
                  <span class="crm-pill crm-pill-progress">In Bay</span>
                <?php elseif ($apt['status'] === 'COMPLETED'): ?>
                  <span class="crm-pill crm-pill-success">Completed</span>
                <?php else: ?>
                  <span class="crm-pill crm-pill-secondary"><?= htmlspecialchars($apt['status']) ?></span>
                <?php endif; ?>
              </td>
              <td>
                <span class="crm-source-badge"><?= strtoupper(htmlspecialchars($apt['source'] ?? 'DIRECT')) ?></span>
              </td>
              <td>
                <button class="crm-btn crm-btn-xs crm-btn-ghost" onclick="quickCheckIn('<?= addslashes($apt['customer_name'] ?? 'Customer') ?>', '<?= addslashes(($apt['brand'] ?? '') . ' ' . ($apt['model'] ?? '')) ?>', '<?= addslashes($apt['service_name'] ?? '') ?>')">
                  Check-in
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" style="text-align:center; padding:44px 20px; color:#888888;">
                <div style="font-size:32px; margin-bottom:10px;">📅</div>
                <strong style="color:#ffffff; font-size:14px;">No Appointments Booked Yet</strong>
                <p style="font-size:12px; margin:6px 0 0; color:#666666;">New bookings submitted via the 7-step booking engine will appear here in real time.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
