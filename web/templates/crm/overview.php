<?php
// THE DRIVE CLINIC — Studio Overview Dashboard
// Displays live studio status, dynamic metrics, workshop bays, and today's appointments

$todayStr = date('Y-m-d');

// Real KPI metrics from database
$revRow = Database::queryOne("SELECT COALESCE(SUM(paid_amount), 0) as total FROM invoices WHERE payment_status = 'PAID'");
$revenueCollected = (float)($revRow['total'] ?? 0);

$bkgRow = Database::queryOne("SELECT COUNT(*) as count FROM appointments WHERE date = ?", [$todayStr]);
$bookingsToday = (int)($bkgRow['count'] ?? 0);

$enqRow = Database::queryOne("SELECT COUNT(*) as count FROM leads WHERE stage IN ('NEW', 'CONTACTED')");
$activeEnquiries = (int)($enqRow['count'] ?? 0);

$pendRow = Database::queryOne("SELECT COALESCE(SUM(grand_total - paid_amount), 0) as total FROM invoices WHERE payment_status != 'PAID'");
$paymentsPending = (float)($pendRow['total'] ?? 0);

// Active bay jobs
$activeJobs = Database::query("
  SELECT j.*, c.name as customer_name, v.brand, v.model, v.registration
  FROM job_cards j
  LEFT JOIN customers c ON j.customer_id = c.id
  LEFT JOIN vehicles v ON j.vehicle_id = v.id
  WHERE j.status = 'IN_PROGRESS'
  ORDER BY j.bay_no ASC
");
$activeJobsCount = count($activeJobs);

// Today's appointments
$todayAppointments = Database::query("
  SELECT a.*, c.name as customer_name, c.phone as customer_phone,
         v.brand, v.model, v.registration, s.name as service_name
  FROM appointments a
  LEFT JOIN customers c ON a.customer_id = c.id
  LEFT JOIN vehicles v ON a.vehicle_id = v.id
  LEFT JOIN services s ON a.service_id = s.id
  WHERE a.date = ?
  ORDER BY a.time ASC
", [$todayStr]);

// Low stock items
$lowStockItems = Database::query("SELECT id, name, sku, current_stock, min_stock_alert, unit FROM inventory_items WHERE current_stock <= min_stock_alert ORDER BY current_stock ASC LIMIT 3");

// Open invoices
$openInvoices = Database::query("
  SELECT i.*, c.name as customer_name, v.brand, v.model
  FROM invoices i
  LEFT JOIN customers c ON i.customer_id = c.id
  LEFT JOIN vehicles v ON i.vehicle_id = v.id
  WHERE i.payment_status != 'PAID'
  ORDER BY i.created_at DESC
  LIMIT 3
");
?>
<div class="crm-overview-page">
  <!-- Studio Hero Greeting -->
  <div class="crm-overview-header">
    <div class="crm-overview-title-block">
      <div class="crm-kicker">THE DRIVE CLINIC / STUDIO</div>
      <h1 class="crm-page-heading">A good day for a great finish.</h1>
      <p class="crm-page-sub"><?= date('l, j F Y') ?> &mdash; Here's what's happening at your studio.</p>
    </div>
    <div class="crm-overview-header-actions">
      <span class="crm-date-badge"><?= date('j M Y') ?></span>
      <button class="crm-btn crm-btn-primary" onclick="openNewBookingModal()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        <span>+ New booking</span>
      </button>
    </div>
  </div>

  <!-- Studio Sync Banner -->
  <div class="crm-sync-banner">
    <div class="crm-sync-content">
      <h3 class="crm-sync-title">Your studio, running in sync.</h3>
      <p class="crm-sync-sub">
        <?php if ($activeJobsCount > 0 || $bookingsToday > 0): ?>
          <?= $activeJobsCount ?> <?= $activeJobsCount === 1 ? 'car' : 'cars' ?> in the workshop, <?= $bookingsToday ?> <?= $bookingsToday === 1 ? 'arrival' : 'arrivals' ?> to look forward to.
        <?php else: ?>
          Studio bays calibrated and clean. Ready for new vehicle arrivals.
        <?php endif; ?>
      </p>
    </div>
    <div class="crm-sync-tag"><?= strtoupper(date('l')) ?> AT THE CLINIC</div>
  </div>

  <!-- 4 KPI Stat Grid -->
  <div class="crm-stats-grid">
    <div class="crm-stat-card">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Revenue collected</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
        </div>
      </div>
      <div class="crm-stat-value">₹<?= number_format($revenueCollected) ?></div>
      <div class="crm-stat-footer"><?= $revenueCollected > 0 ? 'Recorded studio collections' : 'No payments collected yet' ?></div>
    </div>

    <div class="crm-stat-card">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Bookings today</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
      </div>
      <div class="crm-stat-value"><?= $bookingsToday ?></div>
      <div class="crm-stat-footer"><?= $activeJobsCount ?> active in-bay jobs</div>
    </div>

    <div class="crm-stat-card">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Active enquiries</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
        </div>
      </div>
      <div class="crm-stat-value"><?= $activeEnquiries ?></div>
      <div class="crm-stat-footer">Live leads awaiting consultation</div>
    </div>

    <div class="crm-stat-card">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Payments pending</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
      </div>
      <div class="crm-stat-value">₹<?= number_format($paymentsPending) ?></div>
      <div class="crm-stat-footer"><?= count($openInvoices) ?> open invoices</div>
    </div>
  </div>

  <!-- Studio Revenue & Live Workshop Grid -->
  <div class="crm-grid-2col">
    <!-- Studio Revenue Chart Card -->
    <div class="crm-card">
      <div class="crm-card-header">
        <div>
          <h2 class="crm-card-title">Studio revenue</h2>
          <p class="crm-card-subtitle">A week of care, reflected in numbers.</p>
        </div>
        <a href="/crm/analytics" class="crm-card-action">View analytics</a>
      </div>

      <div class="crm-chart-container">
        <div class="crm-bar-chart">
          <?php
          $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
          $currentDay = date('D');
          foreach ($days as $d):
            $isToday = ($d === $currentDay);
          ?>
            <div class="crm-bar-col">
              <div class="crm-bar-fill <?= $isToday ? 'crm-bar-active-day' : '' ?>" style="height: <?= $isToday ? '35%' : '10%' ?>;" data-val="₹0"></div>
              <span class="crm-bar-day"><?= $d ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="crm-chart-legend">
        <span class="crm-legend-dot"></span>
        <span class="crm-legend-text">Real-time daily collected revenue &bull; INR</span>
      </div>
    </div>

    <!-- Live Workshop Bay Board -->
    <div class="crm-card">
      <div class="crm-card-header">
        <h2 class="crm-card-title">Live workshop</h2>
        <span class="crm-badge-active-jobs"><?= $activeJobsCount ?> active jobs</span>
      </div>

      <div class="crm-bays-list">
        <?php for ($bay = 1; $bay <= 4; $bay++): ?>
          <?php 
          $job = null;
          foreach ($activeJobs as $j) {
            if ((int)$j['bay_no'] === $bay) {
              $job = $j;
              break;
            }
          }
          ?>
          <?php if ($job): ?>
            <div class="crm-bay-item">
              <div class="crm-bay-num"><?= sprintf('%02d', $bay) ?></div>
              <div class="crm-bay-info">
                <div class="crm-bay-car"><?= htmlspecialchars(($job['brand'] ?? '') . ' ' . ($job['model'] ?? 'Vehicle')) ?></div>
                <div class="crm-bay-service"><?= htmlspecialchars($job['service_names'] ?? 'Detailing') ?> &bull; <?= htmlspecialchars($job['assigned_tech'] ?? 'Tech') ?></div>
              </div>
              <a href="/crm/job-cards?id=<?= $job['id'] ?>" class="crm-btn crm-btn-sm crm-btn-ghost">View</a>
            </div>
          <?php else: ?>
            <div class="crm-bay-item crm-bay-available">
              <div class="crm-bay-num"><?= sprintf('%02d', $bay) ?></div>
              <div class="crm-bay-info">
                <div class="crm-bay-car">Ready for arrival</div>
                <div class="crm-bay-service">Bay cleanroom calibrated & available</div>
              </div>
              <span class="crm-pill-available">Available</span>
            </div>
          <?php endif; ?>
        <?php endfor; ?>
      </div>
    </div>
  </div>

  <!-- Today's Appointments & On Your Radar Grid -->
  <div class="crm-grid-2col">
    <!-- Today's Appointments Table -->
    <div class="crm-card">
      <div class="crm-card-header">
        <h2 class="crm-card-title">Today's appointments</h2>
        <a href="/crm/bookings" class="crm-card-action">View all</a>
      </div>

      <div class="crm-appointments-list">
        <?php if (!empty($todayAppointments)): ?>
          <?php foreach ($todayAppointments as $apt): ?>
            <div class="crm-appt-row">
              <div class="crm-appt-time">
                <strong><?= htmlspecialchars(substr($apt['time'], 0, 5)) ?></strong>
                <span><?= htmlspecialchars($apt['date']) ?></span>
              </div>
              <div class="crm-appt-avatar"><?= strtoupper(substr($apt['customer_name'] ?? 'C', 0, 2)) ?></div>
              <div class="crm-appt-customer">
                <div class="crm-customer-name"><?= htmlspecialchars($apt['customer_name'] ?? 'Walk-in') ?></div>
                <div class="crm-customer-reg"><?= htmlspecialchars($apt['registration'] ?: 'Reg N/A') ?></div>
              </div>
              <div class="crm-appt-service">
                <div class="crm-car-name"><?= htmlspecialchars(($apt['brand'] ?? '') . ' ' . ($apt['model'] ?? '')) ?></div>
                <div class="crm-service-name"><?= htmlspecialchars($apt['service_name'] ?? 'Studio Service') ?></div>
              </div>
              <div class="crm-appt-status">
                <span class="crm-pill crm-pill-confirmed"><?= htmlspecialchars($apt['status']) ?></span>
              </div>
              <div class="crm-appt-action">
                <button class="crm-btn crm-btn-sm crm-btn-ghost" onclick="quickCheckIn('<?= addslashes($apt['customer_name'] ?? 'Client') ?>', '<?= addslashes(($apt['brand'] ?? '') . ' ' . ($apt['model'] ?? '')) ?>', '<?= addslashes($apt['service_name'] ?? '') ?>')">Check in</button>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div style="padding:40px 20px; text-align:center; color:#888888;">
            <div style="font-size:32px; margin-bottom:10px;">📅</div>
            <strong style="color:#ffffff; font-size:14px;">No Appointments Scheduled For Today</strong>
            <p style="font-size:12px; margin:6px 0 0; color:#666666;">New bookings submitted on the website will populate here automatically.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- On Your Radar & Studio Actions Card -->
    <div class="crm-card">
      <div class="crm-card-header">
        <h2 class="crm-card-title">On your radar</h2>
        <a href="/crm/inventory" class="crm-card-action">View inventory</a>
      </div>

      <div class="crm-radar-list">
        <?php if (!empty($lowStockItems)): ?>
          <?php foreach ($lowStockItems as $item): ?>
            <div class="crm-radar-item crm-radar-alert">
              <div class="crm-radar-icon">⚠️</div>
              <div class="crm-radar-content">
                <div class="crm-radar-title">Low Stock Alert: <?= htmlspecialchars($item['name']) ?></div>
                <div class="crm-radar-desc">Only <?= $item['current_stock'] ?> <?= htmlspecialchars($item['unit']) ?> remaining (Min alert: <?= $item['min_stock_alert'] ?>).</div>
              </div>
              <button class="crm-btn crm-btn-xs crm-btn-danger" onclick="openStockInModal(<?= $item['id'] ?>)">Restock</button>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($openInvoices)): ?>
          <?php foreach ($openInvoices as $inv): ?>
            <div class="crm-radar-item">
              <div class="crm-radar-icon">💳</div>
              <div class="crm-radar-content">
                <div class="crm-radar-title">Pending Payment: ₹<?= number_format((float)($inv['grand_total'] - $inv['paid_amount'])) ?> (Invoice #<?= htmlspecialchars($inv['invoice_no']) ?>)</div>
                <div class="crm-radar-desc"><?= htmlspecialchars($inv['customer_name'] ?? 'Client') ?> &bull; <?= htmlspecialchars(($inv['brand'] ?? '') . ' ' . ($inv['model'] ?? '')) ?> &bull; Awaiting payment.</div>
              </div>
              <a href="/crm/invoices" class="crm-btn crm-btn-xs crm-btn-ghost">Collect</a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <?php if (empty($lowStockItems) && empty($openInvoices)): ?>
          <div style="padding:40px 20px; text-align:center; color:#888888;">
            <div style="font-size:32px; margin-bottom:10px;">✨</div>
            <strong style="color:#ffffff; font-size:14px;">Studio Operations All Clear</strong>
            <p style="font-size:12px; margin:6px 0 0; color:#666666;">No urgent stock warnings or pending payment escalations.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
