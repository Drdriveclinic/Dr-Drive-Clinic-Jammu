<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Global Navigation Bar with Ultra-Luxury Mega Dropdown & Mobile Drawer
$activeNav = $activeNav ?? '';
$logoPath = dirname(__DIR__, 2) . '/public/assets/brand/logo-white.svg';
$hasLogo = file_exists($logoPath);
?>
<header class="site-header header-transparent">
  <div class="container nav-container">
    <a href="/" class="nav-brand" aria-label="THE DRIVE CLINIC - Home" data-major-nav>
      <?php if ($hasLogo): ?>
        <img src="/assets/brand/logo-white.svg" alt="THE DRIVE CLINIC" class="nav-logo-img" width="190" height="42" onerror="this.onerror=null; this.src='/assets/brand/logo-official.png';">
      <?php else: ?>
        <img src="/assets/brand/logo-official.png" alt="THE DRIVE CLINIC" class="nav-logo-img" width="190" height="42">
      <?php endif; ?>
    </a>

    <!-- Desktop Navigation Links -->
    <nav class="nav-links" aria-label="Main Navigation">
      <a href="/" class="nav-link <?= $activeNav === 'home' ? 'active' : '' ?>" data-major-nav>HOME</a>
      
      <!-- Services Item with Luxury Mega Dropdown -->
      <div class="nav-item-dropdown">
        <a href="/services" class="nav-link nav-dropdown-trigger <?= $activeNav === 'services' ? 'active' : '' ?>" data-major-nav>
          <span>SERVICES</span>
          <svg class="nav-chevron" width="9" height="5" viewBox="0 0 10 6" fill="none">
            <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>

        <!-- Sleek Automotive Dropdown Menu (Compact & Hyper-Dynamic) -->
        <div class="nav-dropdown-menu">
          <div class="nav-dropdown-list">
            <a href="/services/ppf" class="nav-dropdown-item" data-major-nav>
              <div class="nav-drop-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
              </div>
              <div class="nav-drop-info">
                <span class="nav-drop-title">Paint Protection Film</span>
                <span class="nav-drop-tag">10-YR TPU</span>
              </div>
              <span class="nav-drop-arrow">→</span>
            </a>

            <a href="/services/ceramic-coating" class="nav-dropdown-item" data-major-nav>
              <div class="nav-drop-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
              </div>
              <div class="nav-drop-info">
                <span class="nav-drop-title">9H Ceramic Coating</span>
                <span class="nav-drop-tag">9H MATRIX</span>
              </div>
              <span class="nav-drop-arrow">→</span>
            </a>

            <a href="/services/full-car-detailing" class="nav-dropdown-item" data-major-nav>
              <div class="nav-drop-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 16H9m10 0h3v-3.15a1 1 0 0 0-.84-.99L16 11l-2.7-3.6a1 1 0 0 0-.8-.4H7.5a1 1 0 0 0-.8.4L4 11l-5.16.86a1 1 0 0 0-.84.99V16h3m14 0a2 2 0 1 1-4 0m-8 0a2 2 0 1 1-4 0"></path></svg>
              </div>
              <div class="nav-drop-info">
                <span class="nav-drop-title">Full Car Detailing</span>
                <span class="nav-drop-tag">MULTI-STAGE</span>
              </div>
              <span class="nav-drop-arrow">→</span>
            </a>

            <a href="/services/bike-detailing" class="nav-dropdown-item" data-major-nav>
              <div class="nav-drop-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="5.5" cy="17.5" r="3.5"></circle><circle cx="18.5" cy="17.5" r="3.5"></circle><path d="M15 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-3 11.5L9 9l4.5-2L16 11h3"></path></svg>
              </div>
              <div class="nav-drop-info">
                <span class="nav-drop-title">Superbike Detailing</span>
                <span class="nav-drop-tag">ALLOY SEAL</span>
              </div>
              <span class="nav-drop-arrow">→</span>
            </a>

            <a href="/services/interior-deep-clean" class="nav-dropdown-item" data-major-nav>
              <div class="nav-drop-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
              </div>
              <div class="nav-drop-info">
                <span class="nav-drop-title">Interior Deep Clean</span>
                <span class="nav-drop-tag">180°C STEAM</span>
              </div>
              <span class="nav-drop-arrow">→</span>
            </a>

            <a href="/services/ceramic-wash" class="nav-dropdown-item" data-major-nav>
              <div class="nav-drop-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
              </div>
              <div class="nav-drop-info">
                <span class="nav-drop-title">Ceramic SiO2 Wash</span>
                <span class="nav-drop-tag">PH-NEUTRAL</span>
              </div>
              <span class="nav-drop-arrow">→</span>
            </a>
          </div>

          <div class="nav-dropdown-footer">
            <a href="/services" class="nav-dropdown-all-btn" data-major-nav>
              <span>ALL 15 SERVICES</span>
              <span class="nav-drop-arrow-all">→</span>
            </a>
          </div>
        </div>
      </div>

      <a href="/memberships" class="nav-link <?= $activeNav === 'memberships' ? 'active' : '' ?>" data-major-nav>
        MEMBERSHIPS <span class="nav-pill-badge">VIP PASS</span>
      </a>
      <a href="/our-work" class="nav-link <?= $activeNav === 'our-work' ? 'active' : '' ?>" data-major-nav>OUR WORK</a>
      <a href="/about" class="nav-link <?= $activeNav === 'about' ? 'active' : '' ?>" data-major-nav>ABOUT</a>
      <a href="/contact" class="nav-link <?= $activeNav === 'contact' ? 'active' : '' ?>" data-major-nav>CONTACT</a>
    </nav>

    <!-- Header Actions -->
    <div class="nav-actions">
      <a href="/book" class="btn btn-red" data-cursor="BOOK" data-major-nav>BOOK NOW →</a>
      <button class="nav-toggle-btn" aria-label="Open Navigation Menu" aria-expanded="false" id="mobileNavToggle">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </div>
</header>

<!-- Mobile Full-Screen Navigation Drawer -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" aria-hidden="true">
  <div class="mobile-menu-inner">
    <div class="mobile-menu-brand-header" style="padding-bottom: 20px; margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: space-between;">
      <a href="/" data-major-nav style="display:inline-block;">
        <img src="/assets/brand/logo-white.svg" alt="THE DRIVE CLINIC" style="height:38px; width:auto; max-width:190px; display:block;" onerror="this.onerror=null; this.src='/assets/brand/logo-official.png';">
      </a>
    </div>
    <nav class="mobile-menu-nav">
      <a href="/" class="mobile-menu-link <?= $activeNav === 'home' ? 'active' : '' ?>" data-major-nav>HOME</a>
      
      <!-- Mobile Expandable Services Accordion Item -->
      <div class="mobile-accordion-item" id="mobileServicesAccordion">
        <div class="mobile-accordion-header">
          <a href="/services" class="mobile-menu-link <?= $activeNav === 'services' ? 'active' : '' ?>" data-major-nav>SERVICES</a>
          <button type="button" class="mobile-accordion-btn" id="mobileServicesToggleBtn" aria-label="Toggle Service List">
            <svg class="mobile-accordion-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>
        </div>
        <div class="mobile-accordion-content" id="mobileServicesContent">
          <div class="mobile-sublinks-grid">
            <a href="/services/ppf" class="mobile-sublink" data-major-nav>
              <div class="mobile-sublink-badge-icon">🛡️</div>
              <div class="sublink-text">
                <strong>Paint Protection Film (PPF)</strong>
                <small>Self-healing TPU stone-chip armor • 10-Yr Warranty</small>
              </div>
            </a>
            <a href="/services/ceramic-coating" class="mobile-sublink" data-major-nav>
              <div class="mobile-sublink-badge-icon">✨</div>
              <div class="sublink-text">
                <strong>9H Ceramic & Graphene Coating</strong>
                <small>Liquid quartz candy gloss & 110° water beading</small>
              </div>
            </a>
            <a href="/services/full-car-detailing" class="mobile-sublink" data-major-nav>
              <div class="mobile-sublink-badge-icon">🚗</div>
              <div class="sublink-text">
                <strong>Full Car Detailing</strong>
                <small>Multi-stage swirl correction & cabin rejuvenation</small>
              </div>
            </a>
            <a href="/services/bike-detailing" class="mobile-sublink" data-major-nav>
              <div class="mobile-sublink-badge-icon">🏍️</div>
              <div class="sublink-text">
                <strong>Superbike Detailing</strong>
                <small>Mechanical degreasing & ceramic exhaust shield</small>
              </div>
            </a>
            <a href="/services/interior-deep-clean" class="mobile-sublink" data-major-nav>
              <div class="mobile-sublink-badge-icon">💨</div>
              <div class="sublink-text">
                <strong>Interior Steam & Sanitization</strong>
                <small>180°C heated extraction & antibacterial AC purge</small>
              </div>
            </a>
            <a href="/services/ceramic-wash" class="mobile-sublink" data-major-nav>
              <div class="mobile-sublink-badge-icon">💧</div>
              <div class="sublink-text">
                <strong>Ceramic SiO2 Foam Wash</strong>
                <small>pH-neutral SiO2 hydrophobic wash</small>
              </div>
            </a>
            <a href="/services" class="mobile-sublink-all" data-major-nav>
              <span>Explore All Detailing Packages &rarr;</span>
            </a>
          </div>
        </div>
      </div>

      <a href="/memberships" class="mobile-menu-link <?= $activeNav === 'memberships' ? 'active' : '' ?>" data-major-nav>
        MEMBERSHIPS <span class="mobile-vip-badge">VIP PASS</span>
      </a>
      <a href="/our-work" class="mobile-menu-link <?= $activeNav === 'our-work' ? 'active' : '' ?>" data-major-nav>OUR WORK</a>
      <a href="/about" class="mobile-menu-link <?= $activeNav === 'about' ? 'active' : '' ?>" data-major-nav>ABOUT</a>
      <a href="/contact" class="mobile-menu-link <?= $activeNav === 'contact' ? 'active' : '' ?>" data-major-nav>CONTACT</a>
    </nav>

    <div class="mobile-menu-footer">
      <a href="/book" class="btn btn-red mobile-book-btn" data-major-nav>BOOK STUDIO APPOINTMENT →</a>
      <div class="mobile-studio-info">
        <div class="mobile-info-item">📍 Nanak Nagar, Jammu</div>
        <div class="mobile-info-item">📞 <?= htmlspecialchars($settings['phone'] ?? '+91 94191 00000') ?></div>
      </div>
    </div>
  </div>
</div>
