<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Global Dark Footer
$logoPath = dirname(__DIR__, 2) . '/public/assets/brand/logo-white.svg';
$hasLogo = file_exists($logoPath);
$year = date('Y');
?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-top">
      <!-- Column 1: Brand & Tagline -->
      <div>
        <a href="/" class="nav-brand" aria-label="The Drive Clinic">
          <?php if ($hasLogo): ?>
            <img src="/assets/brand/logo-white.svg" alt="The Drive Clinic" class="nav-logo-img" width="180" height="36">
          <?php else: ?>
            <div class="logo-fallback">[ OFFICIAL LOGO ]</div>
          <?php endif; ?>
        </a>
        <h3 class="footer-brand-title">WHERE CARS GET<br>THEIR SHINE BACK.</h3>
        <p class="footer-tagline">Premium car and bike detailing, Paint Protection Film (PPF), and studio wash in Jammu.</p>
        <a href="/book" class="btn btn-red" data-cursor="BOOK">BOOK APPOINTMENT →</a>
      </div>

      <!-- Column 2: Services -->
      <div>
        <div class="footer-col-title">SERVICES</div>
        <ul class="footer-list">
          <li><a href="/services/ppf" class="footer-list-link">Paint Protection Film (PPF)</a></li>
          <li><a href="/services/bike-detailing" class="footer-list-link">Bike Detailing</a></li>
          <li><a href="/services/ceramic-wash" class="footer-list-link">Ceramic Wash</a></li>
          <li><a href="/services/steam-wash" class="footer-list-link">Steam Wash</a></li>
          <li><a href="/services/maintenance-wash" class="footer-list-link">Maintenance Wash</a></li>
          <li><a href="/services/full-car-detailing" class="footer-list-link">Full Car Detailing</a></li>
          <li><a href="/services" class="footer-list-link" style="color:var(--red);">View All Services →</a></li>
        </ul>
      </div>

      <!-- Column 3: Company -->
      <div>
        <div class="footer-col-title">COMPANY</div>
        <ul class="footer-list">
          <li><a href="/about" class="footer-list-link">About Us</a></li>
          <li><a href="/our-work" class="footer-list-link">Our Work</a></li>
          <li><a href="/services" class="footer-list-link">Service Menu</a></li>
          <li><a href="/contact" class="footer-list-link">Contact Studio</a></li>
          <li><a href="/book" class="footer-list-link">Book Online</a></li>
        </ul>
      </div>

      <!-- Column 4: Studio Contact -->
      <div>
        <div class="footer-col-title">STUDIO</div>
        <ul class="footer-list">
          <li style="color:rgba(255,255,255,0.85); font-size:14px; line-height:1.6;">
            <?= htmlspecialchars($settings['address'] ?? 'Nanak Nagar, Jammu, J&K 180004, India') ?>
          </li>
          <li style="margin-top:8px;">
            <a href="tel:<?= htmlspecialchars($settings['phone'] ?? '+91 94191 00000') ?>" class="footer-list-link" style="color:var(--white); font-weight:700;">
              <?= htmlspecialchars($settings['phone'] ?? '+91 94191 00000') ?>
            </a>
          </li>
          <li>
            <a href="https://wa.me/<?= htmlspecialchars($settings['whatsapp'] ?? '919419100000') ?>" target="_blank" rel="noopener" class="footer-list-link">
              WhatsApp: <?= htmlspecialchars($settings['whatsapp'] ?? '919419100000') ?>
            </a>
          </li>
          <li>
            <a href="mailto:<?= htmlspecialchars($settings['email'] ?? 'info@thedriveclinic.com') ?>" class="footer-list-link">
              <?= htmlspecialchars($settings['email'] ?? 'info@thedriveclinic.com') ?>
            </a>
          </li>
          <li style="color:var(--text-3); font-size:13px; margin-top:8px;">
            Hours: <?= htmlspecialchars($settings['hours'] ?? 'Mon - Sun: 09:00 AM - 08:00 PM') ?>
          </li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <div>&copy; <?= $year ?> THE DRIVE CLINIC. ALL RIGHTS RESERVED.</div>
      <div style="display:flex; gap:24px;">
        <span style="color:var(--text-3);">NANAK NAGAR, JAMMU</span>
        <a href="#top" class="footer-list-link">BACK TO TOP ↑</a>
      </div>
    </div>
  </div>
</footer>
