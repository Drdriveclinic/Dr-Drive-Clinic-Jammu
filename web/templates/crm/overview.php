<?php
// THE DRIVE CLINIC — Studio Overview Dashboard
// Displays real-time studio status, revenue metrics, live workshop bays, and today's appointments
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
      <p class="crm-sync-sub">3 cars in the workshop, 3 arrivals to look forward to.</p>
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
      <div class="crm-stat-value">₹1,13,500</div>
      <div class="crm-stat-footer">All recorded demo payments</div>
    </div>

    <div class="crm-stat-card">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Bookings today</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
      </div>
      <div class="crm-stat-value">6</div>
      <div class="crm-stat-footer">3 jobs in progress</div>
    </div>

    <div class="crm-stat-card">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Active enquiries</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
        </div>
      </div>
      <div class="crm-stat-value">8</div>
      <div class="crm-stat-footer">Your next great customer starts here</div>
    </div>

    <div class="crm-stat-card">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Payments pending</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
      </div>
      <div class="crm-stat-value">₹26,500</div>
      <div class="crm-stat-footer">2 open invoices</div>
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
          <div class="crm-bar-col">
            <div class="crm-bar-fill" style="height: 12%;" data-val="₹14,000"></div>
            <span class="crm-bar-day">Sat</span>
          </div>
          <div class="crm-bar-col">
            <div class="crm-bar-fill" style="height: 8%;" data-val="₹9,500"></div>
            <span class="crm-bar-day">Sun</span>
          </div>
          <div class="crm-bar-col">
            <div class="crm-bar-fill crm-bar-highlight" style="height: 92%;" data-val="₹1,13,500"></div>
            <span class="crm-bar-day">Mon</span>
          </div>
          <div class="crm-bar-col">
            <div class="crm-bar-fill" style="height: 18%;" data-val="₹22,000"></div>
            <span class="crm-bar-day">Tue</span>
          </div>
          <div class="crm-bar-col">
            <div class="crm-bar-fill" style="height: 15%;" data-val="₹18,500"></div>
            <span class="crm-bar-day">Wed</span>
          </div>
          <div class="crm-bar-col">
            <div class="crm-bar-fill" style="height: 6%;" data-val="₹7,000"></div>
            <span class="crm-bar-day">Thu</span>
          </div>
          <div class="crm-bar-col">
            <div class="crm-bar-fill crm-bar-active-day" style="height: 25%;" data-val="₹32,500"></div>
            <span class="crm-bar-day">Fri</span>
          </div>
        </div>
      </div>
      <div class="crm-chart-legend">
        <span class="crm-legend-dot"></span>
        <span class="crm-legend-text">Collected revenue &bull; 26 Sep &ndash; 2 Oct &bull; INR</span>
      </div>
    </div>

    <!-- Live Workshop Bay Board -->
    <div class="crm-card">
      <div class="crm-card-header">
        <h2 class="crm-card-title">Live workshop</h2>
        <span class="crm-badge-active-jobs">3 active jobs</span>
      </div>

      <div class="crm-bays-list">
        <!-- Bay 01 -->
        <div class="crm-bay-item">
          <div class="crm-bay-num">01</div>
          <div class="crm-bay-info">
            <div class="crm-bay-car">BMW 320d</div>
            <div class="crm-bay-service">Ceramic shield &bull; Rohan</div>
          </div>
          <a href="/crm/job-cards?id=1" class="crm-btn crm-btn-sm crm-btn-ghost">View</a>
        </div>

        <!-- Bay 02 -->
        <div class="crm-bay-item">
          <div class="crm-bay-num">02</div>
          <div class="crm-bay-info">
            <div class="crm-bay-car">Hyundai Creta</div>
            <div class="crm-bay-service">Signature detailing &bull; Arjun</div>
          </div>
          <a href="/crm/job-cards?id=2" class="crm-btn crm-btn-sm crm-btn-ghost">View</a>
        </div>

        <!-- Bay 03 -->
        <div class="crm-bay-item">
          <div class="crm-bay-num">03</div>
          <div class="crm-bay-info">
            <div class="crm-bay-car">Kia Seltos</div>
            <div class="crm-bay-service">Premium wash &bull; Sameer</div>
          </div>
          <a href="/crm/job-cards?id=3" class="crm-btn crm-btn-sm crm-btn-ghost">View</a>
        </div>

        <!-- Bay 04 -->
        <div class="crm-bay-item crm-bay-available">
          <div class="crm-bay-num">04</div>
          <div class="crm-bay-info">
            <div class="crm-bay-car">Ready for the next arrival</div>
            <div class="crm-bay-service">Keep the day moving beautifully.</div>
          </div>
          <span class="crm-pill-available">Available</span>
        </div>
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
        <!-- Row 1 -->
        <div class="crm-appt-row">
          <div class="crm-appt-time">
            <strong>10:00</strong>
            <span><?= date('Y-m-d') ?></span>
          </div>
          <div class="crm-appt-avatar">RN</div>
          <div class="crm-appt-customer">
            <div class="crm-customer-name">Rahul Nair</div>
            <div class="crm-customer-reg">MH 02 GN 1648</div>
          </div>
          <div class="crm-appt-service">
            <div class="crm-car-name">Kia Seltos</div>
            <div class="crm-service-name">Premium wash</div>
          </div>
          <div class="crm-appt-status">
            <span class="crm-pill crm-pill-progress">In progress</span>
          </div>
          <div class="crm-appt-tech">
            <div class="crm-tech-avatar">S</div>
            <span>Sameer</span>
          </div>
          <div class="crm-appt-action">
            <a href="/crm/job-cards?id=3" class="crm-btn crm-btn-sm crm-btn-ghost">View job</a>
          </div>
        </div>

        <!-- Row 2 -->
        <div class="crm-appt-row">
          <div class="crm-appt-time">
            <strong>14:00</strong>
            <span><?= date('Y-m-d') ?></span>
          </div>
          <div class="crm-appt-avatar">SK</div>
          <div class="crm-appt-customer">
            <div class="crm-customer-name">Sneha Kapoor</div>
            <div class="crm-customer-reg">MH 01 DJ 9009</div>
          </div>
          <div class="crm-appt-service">
            <div class="crm-car-name">Mercedes GLC</div>
            <div class="crm-service-name">Full-body PPF</div>
          </div>
          <div class="crm-appt-status">
            <span class="crm-pill crm-pill-confirmed">Confirmed</span>
          </div>
          <div class="crm-appt-tech">
            <div class="crm-tech-avatar">R</div>
            <span>Rohan</span>
          </div>
          <div class="crm-appt-action">
            <button class="crm-btn crm-btn-sm crm-btn-ghost" onclick="quickCheckIn('Sneha Kapoor', 'Mercedes GLC', 'PPF')">Check in</button>
          </div>
        </div>

        <!-- Row 3 -->
        <div class="crm-appt-row">
          <div class="crm-appt-time">
            <strong>15:00</strong>
            <span><?= date('Y-m-d') ?></span>
          </div>
          <div class="crm-appt-avatar">VD</div>
          <div class="crm-appt-customer">
            <div class="crm-customer-name">Vikram Desai</div>
            <div class="crm-customer-reg">JK 02 BB 1020</div>
          </div>
          <div class="crm-appt-service">
            <div class="crm-car-name">Toyota Fortuner</div>
            <div class="crm-service-name">Ceramic Coating</div>
          </div>
          <div class="crm-appt-status">
            <span class="crm-pill crm-pill-confirmed">Confirmed</span>
          </div>
          <div class="crm-appt-tech">
            <div class="crm-tech-avatar">A</div>
            <span>Arjun</span>
          </div>
          <div class="crm-appt-action">
            <button class="crm-btn crm-btn-sm crm-btn-ghost" onclick="quickCheckIn('Vikram Desai', 'Toyota Fortuner', 'Ceramic')">Check in</button>
          </div>
        </div>

        <!-- Row 4 -->
        <div class="crm-appt-row">
          <div class="crm-appt-time">
            <strong>17:00</strong>
            <span><?= date('Y-m-d') ?></span>
          </div>
          <div class="crm-appt-avatar">KS</div>
          <div class="crm-appt-customer">
            <div class="crm-customer-name">Kabir Singh</div>
            <div class="crm-customer-reg">JK 02 AC 4004</div>
          </div>
          <div class="crm-appt-service">
            <div class="crm-car-name">BMW 320d</div>
            <div class="crm-service-name">Ceramic Shield</div>
          </div>
          <div class="crm-appt-status">
            <span class="crm-pill crm-pill-progress">In progress</span>
          </div>
          <div class="crm-appt-tech">
            <div class="crm-tech-avatar">R</div>
            <span>Rohan</span>
          </div>
          <div class="crm-appt-action">
            <a href="/crm/job-cards?id=1" class="crm-btn crm-btn-sm crm-btn-ghost">View job</a>
          </div>
        </div>
      </div>
    </div>

    <!-- On Your Radar & Studio Actions Card -->
    <div class="crm-card">
      <div class="crm-card-header">
        <h2 class="crm-card-title">On your radar</h2>
        <a href="/crm/inventory" class="crm-card-action">View inventory</a>
      </div>

      <div class="crm-radar-list">
        <!-- Stock Alert -->
        <div class="crm-radar-item crm-radar-alert">
          <div class="crm-radar-icon">⚠️</div>
          <div class="crm-radar-content">
            <div class="crm-radar-title">Low Stock Alert: STEK DYNOshield PPF</div>
            <div class="crm-radar-desc">Only 2.5 rolls remaining (Min alert: 2.0). 2 full-body wraps scheduled this weekend.</div>
          </div>
          <button class="crm-btn crm-btn-xs crm-btn-danger" onclick="openStockInModal(1)">Restock</button>
        </div>

        <!-- Decline Log Notice -->
        <div class="crm-radar-item crm-radar-decline">
          <div class="crm-radar-icon">📋</div>
          <div class="crm-radar-content">
            <div class="crm-radar-title">Recent Decline: 0.5 Roll Scrapped (₹24,250 loss)</div>
            <div class="crm-radar-desc">Logged by Rohan Sharma &bull; Creased film during front fender stretch &bull; Ref #JC-2026-001</div>
          </div>
          <a href="/crm/inventory#declines" class="crm-btn crm-btn-xs crm-btn-ghost">Audit</a>
        </div>

        <!-- PPF Warranty Inspection -->
        <div class="crm-radar-item">
          <div class="crm-radar-icon">🛡️</div>
          <div class="crm-radar-content">
            <div class="crm-radar-title">Annual PPF Inspection Due: Aman Gupta</div>
            <div class="crm-radar-desc">Audi A6 (JK 02 AB 1234) &bull; 1-Year Top-coat inspection reminder sent.</div>
          </div>
          <a href="/crm/warranty" class="crm-btn crm-btn-xs crm-btn-ghost">View Cert</a>
        </div>

        <!-- Unpaid Invoice -->
        <div class="crm-radar-item">
          <div class="crm-radar-icon">💳</div>
          <div class="crm-radar-content">
            <div class="crm-radar-title">Pending Payment: ₹26,500 (Invoice #INV-2026-004)</div>
            <div class="crm-radar-desc">Pooja Sharma &bull; Jeep Compass Paint Enhancement &bull; Awaiting UPI confirmation.</div>
          </div>
          <a href="/crm/invoices" class="crm-btn crm-btn-xs crm-btn-ghost">Collect</a>
        </div>
      </div>
    </div>
  </div>
</div>
