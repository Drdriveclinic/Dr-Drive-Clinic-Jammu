<?php
// THE DRIVE CLINIC — Job Cards & Workshop Bays
$jobCards = Database::query("
  SELECT j.*, c.name as customer_name, c.phone as customer_phone, 
         v.brand, v.model, v.registration, v.color
  FROM job_cards j
  LEFT JOIN customers c ON j.customer_id = c.id
  LEFT JOIN vehicles v ON j.vehicle_id = v.id
  ORDER BY j.created_at DESC
");

$activeJobs = array_filter($jobCards, fn($j) => ($j['status'] ?? '') === 'IN_PROGRESS');
?>
<div class="crm-jobcards-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">OPERATIONS &bull; WORKSHOP FLOOR</div>
      <h1 class="crm-page-heading">Live Job Cards & Digital Bay Board</h1>
      <p class="crm-page-sub">Track active bay work, pre-service vehicle inspection, paint defects, and consumables used.</p>
    </div>
    <div class="crm-page-actions">
      <button class="crm-btn crm-btn-primary" onclick="openNewBookingModal()">+ New Job Card</button>
    </div>
  </div>

  <!-- Workshop Bay Grid -->
  <div class="crm-bay-status-cards">
    <?php
    $bayConfigs = [
      1 => ['name' => 'BAY 01 &bull; DUST-FREE CLEANROOM', 'type' => 'PPF & Cleanroom'],
      2 => ['name' => 'BAY 02 &bull; PAINT CORRECTION', 'type' => 'Ceramic & Correction'],
      3 => ['name' => 'BAY 03 &bull; DECON & WASH', 'type' => 'Steam & Foam Decon'],
      4 => ['name' => 'BAY 04 &bull; PPF INSTALLATION', 'type' => 'Film Wrap & Finishing']
    ];

    for ($b = 1; $b <= 4; $b++):
      $bayJob = null;
      foreach ($activeJobs as $aj) {
        if ((int)($aj['bay_no'] ?? 0) === $b) {
          $bayJob = $aj;
          break;
        }
      }
    ?>
      <?php if ($bayJob): ?>
        <div class="crm-bay-card crm-bay-active">
          <div class="crm-bay-card-header">
            <span class="crm-bay-badge"><?= $bayConfigs[$b]['name'] ?></span>
            <span class="crm-pill crm-pill-progress">In Progress</span>
          </div>
          <div class="crm-bay-car-title"><?= htmlspecialchars(($bayJob['brand'] ?? '') . ' ' . ($bayJob['model'] ?? 'Car')) ?></div>
          <div class="crm-bay-reg-plate"><?= htmlspecialchars($bayJob['registration'] ?: 'Pending Reg') ?></div>
          <div class="crm-bay-details">
            <div class="crm-bay-detail-row">
              <span>Customer:</span>
              <strong><?= htmlspecialchars($bayJob['customer_name'] ?? 'Client') ?></strong>
            </div>
            <div class="crm-bay-detail-row">
              <span>Package:</span>
              <strong><?= htmlspecialchars($bayJob['service_names'] ?? 'Detailing') ?></strong>
            </div>
            <div class="crm-bay-detail-row">
              <span>Lead Tech:</span>
              <strong><?= htmlspecialchars($bayJob['assigned_tech'] ?? 'Master Tech') ?></strong>
            </div>
            <div class="crm-bay-detail-row">
              <span>Status:</span>
              <strong class="crm-text-amber">In Service</strong>
            </div>
          </div>
          <div class="crm-bay-footer">
            <span class="crm-card-no">#<?= htmlspecialchars($bayJob['card_no']) ?></span>
            <button class="crm-btn crm-btn-xs crm-btn-ghost" onclick="showJobCardModal(<?= $bayJob['id'] ?>)">Inspect Card</button>
          </div>
        </div>
      <?php else: ?>
        <div class="crm-bay-card crm-bay-idle">
          <div class="crm-bay-card-header">
            <span class="crm-bay-badge"><?= $bayConfigs[$b]['name'] ?></span>
            <span class="crm-pill crm-pill-success">Available</span>
          </div>
          <div class="crm-bay-empty-state">
            <div class="crm-empty-icon">🚗</div>
            <h4>Bay Cleaned & Calibrated</h4>
            <p>Ready for next vehicle arrival</p>
          </div>
          <div class="crm-bay-footer">
            <button class="crm-btn crm-btn-sm crm-btn-primary" onclick="openNewBookingModal()">Assign Vehicle</button>
          </div>
        </div>
      <?php endif; ?>
    <?php endfor; ?>
  </div>

  <!-- Job Cards Table -->
  <div class="crm-card">
    <div class="crm-card-header">
      <h2 class="crm-card-title">All Studio Job Cards</h2>
      <span class="crm-text-muted"><?= count($jobCards) ?> Active Records</span>
    </div>

    <div class="crm-table-container">
      <table class="crm-table">
        <thead>
          <tr>
            <th>Job Card #</th>
            <th>Customer & Contact</th>
            <th>Vehicle Details</th>
            <th>Services Assigned</th>
            <th>Assigned Tech</th>
            <th>Job Status</th>
            <th>Billed Amount</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($jobCards)): ?>
            <?php foreach ($jobCards as $jc): ?>
            <tr>
              <td>
                <strong class="crm-job-num"><?= htmlspecialchars($jc['card_no']) ?></strong>
                <div class="crm-text-muted" style="font-size:11px;"><?= date('d M Y', strtotime($jc['created_at'])) ?></div>
              </td>
              <td>
                <strong><?= htmlspecialchars($jc['customer_name'] ?? 'Walk-in') ?></strong>
                <div class="crm-text-muted"><?= htmlspecialchars($jc['customer_phone'] ?? '—') ?></div>
              </td>
              <td>
                <strong><?= htmlspecialchars(($jc['brand'] ?? '') . ' ' . ($jc['model'] ?? '')) ?></strong>
                <div class="crm-plate-tag"><?= htmlspecialchars($jc['registration'] ?: 'Pending Reg') ?></div>
              </td>
              <td>
                <div class="crm-service-tag"><?= htmlspecialchars($jc['service_names']) ?></div>
              </td>
              <td>
                <div class="crm-tech-pill">
                  <span>👤</span> <?= htmlspecialchars($jc['assigned_tech'] ?: 'Unassigned') ?>
                </div>
              </td>
              <td>
                <?php if ($jc['status'] === 'IN_PROGRESS'): ?>
                  <span class="crm-pill crm-pill-progress">In Progress</span>
                <?php elseif ($jc['status'] === 'COMPLETED'): ?>
                  <span class="crm-pill crm-pill-success">Completed</span>
                <?php else: ?>
                  <span class="crm-pill crm-pill-confirmed"><?= htmlspecialchars($jc['status']) ?></span>
                <?php endif; ?>
              </td>
              <td>
                <strong class="crm-amount-val">₹<?= number_format((float)$jc['total_amount']) ?></strong>
              </td>
              <td>
                <button class="crm-btn crm-btn-xs crm-btn-ghost" onclick="alert('Digital inspection checklist loaded for ' + '<?= htmlspecialchars($jc['card_no']) ?>')">
                  View Checklist
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" style="text-align:center; padding:44px 20px; color:#888888;">
                <div style="font-size:32px; margin-bottom:10px;">📋</div>
                <strong style="color:#ffffff; font-size:14px;">No Active Bay Job Cards</strong>
                <p style="font-size:12px; margin:6px 0 0; color:#666666;">Convert a lead from the Leads Pipeline or click "+ New Job Card" above to initiate a bay card.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
