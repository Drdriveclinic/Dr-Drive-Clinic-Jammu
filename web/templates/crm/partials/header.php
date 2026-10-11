<?php
// THE DRIVE CLINIC — CRM Header Partial
$crumbSection = $crumbSection ?? 'Workspace';
$crumbPage = $crumbPage ?? 'Overview';
?>
<header class="crm-topbar">
  <div class="crm-topbar-left">
    <button class="crm-mobile-toggle" id="crmSidebarToggle" aria-label="Toggle Sidebar">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
    </button>
    <div class="crm-breadcrumbs">
      <span class="crm-crumb-root"><?= htmlspecialchars($crumbSection) ?></span>
      <span class="crm-crumb-sep">/</span>
      <span class="crm-crumb-active"><?= htmlspecialchars($crumbPage) ?></span>
    </div>
  </div>

    <div class="crm-workspace-tag" style="background:rgba(34,197,94,0.12); color:#22c55e; border:1px solid rgba(34,197,94,0.3); font-weight:800; font-size:10px; letter-spacing:0.12em; padding:4px 10px; border-radius:4px;">LIVE STUDIO // JAMMU</div>

    <button class="crm-icon-btn" title="Live Studio Notifications" id="crmNotifyBtn">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
      <span class="crm-notify-dot"></span>
    </button>

    <div class="crm-header-avatar" title="Studio Administrator">DC</div>

    <button class="crm-btn crm-btn-primary" id="crmNewBookingBtn" onclick="openNewBookingModal()">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>+ New booking</span>
    </button>
  </div>
</header>
