// THE DRIVE CLINIC — Original Studio Curtain Transition with Smooth Middle Paper Tear
// Scoped strictly to major navigation links (Navbar, Dropdown, Mobile Drawer, Footer, Primary Booking CTA)

export function initPageTransition() {
  const isReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (isReducedMotion) return;

  // Create Transition Overlay in DOM if not already present
  let overlay = document.querySelector('.page-transition-overlay');
  if (!overlay) {
    overlay = document.createElement('div');
    overlay.className = 'page-transition-overlay';
    overlay.innerHTML = `
      <div class="page-transition-redline"></div>
      <div class="page-transition-panel">
        <div class="page-transition-kicker">THE DRIVE CLINIC // JAMMU</div>
        <div class="page-transition-title">THE DRIVE CLINIC</div>
      </div>
      <div class="page-tear-wrap">
        <div class="page-tear-half page-tear-half-top"></div>
        <div class="page-tear-half page-tear-half-bottom"></div>
        <div class="page-tear-seam"></div>
      </div>
    `;
    document.body.appendChild(overlay);
  }

  const redline = overlay.querySelector('.page-transition-redline');
  const panel = overlay.querySelector('.page-transition-panel');
  const title = overlay.querySelector('.page-transition-title');
  const kicker = overlay.querySelector('.page-transition-kicker');
  const seam = overlay.querySelector('.page-tear-seam');
  const tearTop = overlay.querySelector('.page-tear-half-top');
  const tearBottom = overlay.querySelector('.page-tear-half-bottom');

  // Reset Overlay on Page Load / Navigation
  function resetOverlay() {
    overlay.classList.remove('is-active');
    if (redline) redline.style.transform = 'scaleX(0)';
    if (seam) {
      seam.style.opacity = '0';
      seam.style.transform = 'translateY(-50%) scaleX(0)';
    }
    if (tearTop) tearTop.style.transform = 'translateY(0%)';
    if (tearBottom) tearBottom.style.transform = 'translateY(0%)';
    if (title) {
      title.style.opacity = '0';
      title.style.transform = 'translateY(25px)';
    }
    if (kicker) {
      kicker.style.opacity = '0';
      kicker.style.transform = 'translateY(-15px)';
    }
  }

  window.addEventListener('pageshow', resetOverlay);
  resetOverlay();

  // Intercept Major Navigation Links Only
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (!link) return;

    // Only major navigation links trigger the editorial transition
    const isMajorNav = link.hasAttribute('data-major-nav') || 
                       link.matches('.nav-link, .nav-dropdown-item, .nav-dropdown-card, .nav-all-services-link, .nav-dropdown-link, .nav-dropdown-all-btn, .mobile-menu-link, .mobile-sublink, .mobile-sublink-all, .mobile-book-btn, .site-header .btn, .footer-list-link');

    if (!isMajorNav) {
      return;
    }

    const href = link.getAttribute('href');
    const target = link.getAttribute('target');

    // Skip anchors, mailto, tel, downloads, or new tabs
    if (!href || href.startsWith('#') || href.startsWith('javascript:') || 
        href.startsWith('tel:') || href.startsWith('mailto:') || 
        href.startsWith('https://wa.me') || target === '_blank' || 
        e.ctrlKey || e.metaKey || link.hasAttribute('download') || 
        href.includes('/export/')) {
      return;
    }

    // Skip same-page navigation
    if (href === window.location.pathname || href === window.location.href) {
      return;
    }

    e.preventDefault();

    // Determine target title from link text
    let targetText = 'THE DRIVE CLINIC';
    const linkHeading = link.querySelector('strong') || link.querySelector('span');
    if (linkHeading && linkHeading.textContent.trim()) {
      targetText = linkHeading.textContent.trim();
    } else {
      const raw = link.textContent.trim().replace(/[→↑\n\r]/g, '').replace(/VIP/g, '').replace(/PASS/g, '').trim();
      if (raw) targetText = raw;
    }

    // Standardize destination titles for clean editorial luxury
    if (href === '/' || href === '') targetText = 'THE DRIVE CLINIC';
    else if (href === '/services') targetText = 'ALL SERVICES';
    else if (href === '/services/ppf') targetText = 'PAINT PROTECTION FILM';
    else if (href === '/services/ceramic-coating') targetText = 'CERAMIC COATING';
    else if (href === '/services/full-car-detailing') targetText = 'FULL CAR DETAILING';
    else if (href === '/services/bike-detailing') targetText = 'BIKE DETAILING';
    else if (href === '/services/interior-deep-clean') targetText = 'INTERIOR DEEP CLEAN';
    else if (href === '/services/ceramic-wash') targetText = 'CERAMIC WASH';
    else if (href === '/services/steam-wash') targetText = 'STEAM WASH';
    else if (href === '/memberships') targetText = 'VIP MEMBERSHIPS';
    else if (href === '/our-work') targetText = 'STUDIO PORTFOLIO';
    else if (href === '/about') targetText = 'ABOUT THE CLINIC';
    else if (href === '/contact') targetText = 'CONTACT & STUDIO';
    else if (href === '/book') targetText = 'BOOK APPOINTMENT';

    if (title) {
      title.textContent = targetText.toUpperCase();
    }

    // Activate transition overlay
    overlay.classList.add('is-active');

    // Execute Smooth Cinematic GSAP Transition with Middle Paper Tear
    if (window.gsap) {
      const tl = window.gsap.timeline({
        onComplete: () => {
          window.location.href = href;
        }
      });

      // 1. Top Redline sweeps across header (0 -> 220ms)
      tl.to(redline, { scaleX: 1, duration: 0.22, ease: 'power2.inOut' });

      // 2. Middle glowing paper tear seam rips horizontally from center (0 -> 180ms)
      if (seam) {
        tl.fromTo(seam, 
          { scaleX: 0, opacity: 1 }, 
          { scaleX: 1, opacity: 1, duration: 0.2, ease: 'power2.inOut' }, 
          0
        );
      }

      // 3. Dual torn paper halves split smoothly from the middle (0.08s -> 0.44s)
      if (tearTop && tearBottom) {
        tl.to(tearTop, { y: '-104%', duration: 0.36, ease: 'power3.inOut' }, 0.08);
        tl.to(tearBottom, { y: '104%', duration: 0.36, ease: 'power3.inOut' }, 0.08);
      }

      // 4. Fade out glowing seam line as flaps clear (0.22s)
      if (seam) {
        tl.to(seam, { opacity: 0, duration: 0.15, ease: 'power1.out' }, 0.22);
      }

      // 5. Destination kicker & title reveal in studio panel (0.2s -> 0.45s)
      if (kicker) tl.to(kicker, { opacity: 1, y: 0, duration: 0.2, ease: 'power2.out' }, 0.2);
      if (title) tl.to(title, { opacity: 1, y: 0, duration: 0.25, ease: 'power2.out' }, 0.22);

      // 6. Snappy hold before navigation completion (~650ms total)
      tl.to({}, { duration: 0.12 });
    } else {
      window.location.href = href;
    }
  });
}
