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
    <div class="crm-bay-card crm-bay-active">
      <div class="crm-bay-card-header">
        <span class="crm-bay-badge">BAY 01 &bull; DUST-FREE CLEANROOM</span>
        <span class="crm-pill crm-pill-progress">In Progress</span>
      </div>
      <div class="crm-bay-car-title">BMW 320d (Luxury Line)</div>
      <div class="crm-bay-reg-plate">JK 02 AC 4004</div>
      <div class="crm-bay-details">
        <div class="crm-bay-detail-row">
          <span>Customer:</span>
          <strong>Kabir Singh</strong>
        </div>
        <div class="crm-bay-detail-row">
          <span>Package:</span>
          <strong>Ceramic Shield 9H+ (2 Coats)</strong>
        </div>
        <div class="crm-bay-detail-row">
          <span>Lead Tech:</span>
          <strong>Rohan Sharma</strong>
        </div>
        <div class="crm-bay-detail-row">
          <span>Status:</span>
          <strong class="crm-text-amber">Stage 3: 2nd Coat IR Curing</strong>
        </div>
      </div>
      <div class="crm-bay-footer">
        <span class="crm-card-no">#JC-2026-001</span>
        <button class="crm-btn crm-btn-xs crm-btn-ghost" onclick="showJobCardModal(1)">Inspect Card</button>
      </div>
    </div>

    <div class="crm-bay-card crm-bay-active">
      <div class="crm-bay-card-header">
        <span class="crm-bay-badge">BAY 02 &bull; PAINT CORRECTION</span>
        <span class="crm-pill crm-pill-progress">In Progress</span>
      </div>
      <div class="crm-bay-car-title">Hyundai Creta (Knight Edition)</div>
      <div class="crm-bay-reg-plate">JK 02 CD 8821</div>
      <div class="crm-bay-details">
        <div class="crm-bay-detail-row">
          <span>Customer:</span>
          <strong>Arjun Sharma</strong>
        </div>
        <div class="crm-bay-detail-row">
          <span>Package:</span>
          <strong>Signature Detailing + Interior Steam</strong>
        </div>
        <div class="crm-bay-detail-row">
          <span>Lead Tech:</span>
          <strong>Arjun Mehta</strong>
        </div>
        <div class="crm-bay-detail-row">
          <span>Status:</span>
          <strong class="crm-text-amber">Stage 2: Rotary Compound Cut</strong>
        </div>
      </div>
      <div class="crm-bay-footer">
        <span class="crm-card-no">#JC-2026-002</span>
        <button class="crm-btn crm-btn-xs crm-btn-ghost" onclick="showJobCardModal(2)">Inspect Card</button>
      </div>
    </div>

    <div class="crm-bay-card crm-bay-active">
      <div class="crm-bay-card-header">
        <span class="crm-bay-badge">BAY 03 &bull; DECON & WASH</span>
        <span class="crm-pill crm-pill-progress">In Progress</span>
      </div>
      <div class="crm-bay-car-title">Kia Seltos (Gravity Grey)</div>
      <div class="crm-bay-reg-plate">MH 02 GN 1648</div>
      <div class="crm-bay-details">
        <div class="crm-bay-detail-row">
          <span>Customer:</span>
          <strong>Rahul Nair</strong>
        </div>
        <div class="crm-bay-detail-row">
          <span>Package:</span>
          <strong>Premium Signature Foam Wash</strong>
        </div>
        <div class="crm-bay-detail-row">
          <span>Lead Tech:</span>
          <strong>Sameer Khan</strong>
        </div>
        <div class="crm-bay-detail-row">
          <span>Status:</span>
          <strong class="crm-text-amber">Iron Decon & Underbody Blast</strong>
        </div>
      </div>
      <div class="crm-bay-footer">
        <span class="crm-card-no">#JC-2026-003</span>
        <button class="crm-btn crm-btn-xs crm-btn-ghost" onclick="showJobCardModal(3)">Inspect Card</button>
      </div>
    </div>

    <div class="crm-bay-card crm-bay-idle">
      <div class="crm-bay-card-header">
        <span class="crm-bay-badge">BAY 04 &bull; PPF INSTALLATION</span>
        <span class="crm-pill crm-pill-success">Ready</span>
      </div>
      <div class="crm-bay-empty-state">
        <div class="crm-empty-icon">🚗</div>
        <h4>Bay Cleaned & Calibrated</h4>
        <p>Next Arrival: Sneha Kapoor &bull; Mercedes GLC (14:00)</p>
      </div>
      <div class="crm-bay-footer">
        <button class="crm-btn crm-btn-sm crm-btn-primary" onclick="openNewBookingModal()">Assign Vehicle</button>
      </div>
    </div>
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
              <strong><?= htmlspecialchars($jc['brand'] . ' ' . $jc['model']) ?></strong>
              <div class="crm-plate-tag"><?= htmlspecialchars($jc['registration'] ?: 'Pending Reg') ?></div>
            </td>
            <td>
              <div class="crm-service-tag"><?= htmlspecialchars($jc['service_names']) ?></div>
            </td>
            <td>
              <div class="crm-tech-pill">
                <span>👤</span> <?= htmlspecialchars($jc['assigned_tech']) ?>
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
        </tbody>
      </table>
    </div>
  </div>
</div>
