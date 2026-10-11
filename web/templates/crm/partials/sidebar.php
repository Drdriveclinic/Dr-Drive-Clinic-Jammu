<?php
// THE DRIVE CLINIC — CRM Sidebar Navigation
// Structured specifically: Overview, Leads Pipeline, Job Cards, Online Appointments, Inventory Manager, Invoices, Memberships, Customers & Vehicles
$currentNav = $activeNav ?? 'overview';
?>
<aside class="crm-sidebar" id="crmSidebar">
  <div class="crm-sidebar-header">
    <a href="/crm" class="crm-brand-block">
      <img src="/assets/brand/logo-icon.svg" alt="THE DDC" class="crm-brand-icon-svg" style="width:36px; height:36px; border-radius:8px;">
      <div class="crm-brand-text">
        <span class="crm-brand-name">THE DDC</span>
        <span class="crm-brand-sub">STUDIO CRM • JAMMU</span>
      </div>
    </a>

    <div class="crm-studio-pill">
      <div class="crm-studio-avatar">DDC</div>
      <div class="crm-studio-info">
        <span class="crm-studio-title">Flagship Studio</span>
        <span class="crm-studio-role">Owner Workspace</span>
      </div>
      <span class="crm-status-dot" title="Studio Live"></span>
    </div>
  </div>

  <div class="crm-nav-scroll">
    <div class="crm-nav-group">
      <div class="crm-nav-heading">STUDIO OPERATIONS</div>
      <ul class="crm-nav-list">
        <!-- 1. Overview -->
        <li>
          <a href="/crm/overview" class="crm-nav-link <?= $currentNav === 'overview' ? 'active' : '' ?>">
            <svg class="crm-nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            <span>Overview</span>
          </a>
        </li>

        <!-- 2. Leads Pipeline -->
        <li>
          <a href="/crm/leads" class="crm-nav-link <?= $currentNav === 'leads' ? 'active' : '' ?>">
            <svg class="crm-nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
            <span>Leads pipeline</span>
            <span class="crm-badge-counter">8</span>
          </a>
        </li>

        <!-- 3. Job Cards (Customer Visit & Offline Journey) -->
        <li>
          <a href="/crm/job-cards" class="crm-nav-link <?= $currentNav === 'job-cards' ? 'active' : '' ?>">
            <svg class="crm-nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <span>Job cards</span>
            <span class="crm-badge-dot"></span>
          </a>
        </li>

        <!-- 4. Online Appointments -->
        <li>
          <a href="/crm/bookings" class="crm-nav-link <?= $currentNav === 'bookings' ? 'active' : '' ?>">
            <svg class="crm-nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <span>Online appointments</span>
          </a>
        </li>

        <!-- 5. Inventory Manager -->
        <li>
          <a href="/crm/inventory" class="crm-nav-link <?= $currentNav === 'inventory' ? 'active' : '' ?>">
            <svg class="crm-nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            <span>Inventory manager</span>
            <span class="crm-badge-alert">Declines</span>
          </a>
        </li>

        <!-- 6. Invoices (All Invoices Paid/Unpaid) -->
        <li>
          <a href="/crm/invoices" class="crm-nav-link <?= $currentNav === 'invoices' ? 'active' : '' ?>">
            <svg class="crm-nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>
            <span>Invoices</span>
          </a>
        </li>

        <!-- 7. Memberships (Enrolled Club Members & Monthly Packages) -->
        <li>
          <a href="/crm/memberships" class="crm-nav-link <?= $currentNav === 'memberships' ? 'active' : '' ?>">
            <svg class="crm-nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            <span>Memberships</span>
            <span class="crm-badge-counter" style="background:rgba(16,185,129,0.2); color:#10b981;">VIP</span>
          </a>
        </li>

        <!-- 8. Customers & Vehicles (Master Directory at the end) -->
        <li>
          <a href="/crm/customers" class="crm-nav-link <?= $currentNav === 'customers' ? 'active' : '' ?>">
            <svg class="crm-nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            <span>Customers & vehicles</span>
          </a>
        </li>
      </ul>
    </div>
  </div>

  <div class="crm-sidebar-footer">
    <div class="crm-user-profile">
      <div class="crm-user-avatar">AK</div>
      <div class="crm-user-info">
        <span class="crm-user-name">Amit Kapoor</span>
        <span class="crm-user-role">Studio Administrator</span>
      </div>
      <a href="/crm/logout" class="crm-site-link" title="Logout of Studio" style="margin-left:auto;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
      </a>
    </div>
  </div>
</aside>
