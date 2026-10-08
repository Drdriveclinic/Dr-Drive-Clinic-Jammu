<?php
// THE DRIVE CLINIC — CRM Module Generic Container
$moduleTitle = $moduleTitle ?? 'Studio Management Module';
$moduleDesc = $moduleDesc ?? 'Manage studio operations and workflows.';
$moduleBadge = $moduleBadge ?? 'OPERATIONS';
?>
<div class="crm-generic-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker"><?= htmlspecialchars($moduleBadge) ?></div>
      <h1 class="crm-page-heading"><?= htmlspecialchars($moduleTitle) ?></h1>
      <p class="crm-page-sub"><?= htmlspecialchars($moduleDesc) ?></p>
    </div>
  </div>

  <div class="crm-card">
    <div style="padding: 40px 20px; text-align: center;">
      <div style="font-size: 48px; margin-bottom: 16px;">⚡</div>
      <h3 style="font-size: 18px; margin-bottom: 8px; font-weight: 700;">Studio Module Active & Connected</h3>
      <p style="color: #6b7280; max-width: 500px; margin: 0 auto 20px; font-size: 14px;">
        All records and operational data for <strong><?= htmlspecialchars($moduleTitle) ?></strong> are synchronizing with the central SQLite & MySQL studio database.
      </p>
      <button class="crm-btn crm-btn-primary" onclick="openNewBookingModal()">+ Quick Studio Action</button>
    </div>
  </div>
</div>
