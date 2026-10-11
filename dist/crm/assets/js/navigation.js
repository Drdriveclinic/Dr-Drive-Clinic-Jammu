// THE DRIVE CLINIC — Navigation & Mobile Menu Controller

export function initNavigation() {
  const header = document.querySelector('.site-header');
  const toggleBtn = document.querySelector('.nav-toggle-btn');
  const overlay = document.querySelector('.mobile-menu-overlay');
  const overlayLinks = document.querySelectorAll('.mobile-menu-link, .mobile-sublink, .mobile-sublink-all, .mobile-book-btn');

  // Sticky Blur Header
  function handleScroll() {
    if (!header) return;
    if (window.scrollY > 80) {
      header.classList.add('header-scrolled');
      header.classList.remove('header-transparent');
    } else {
      header.classList.remove('header-scrolled');
      header.classList.add('header-transparent');
    }
  }

  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll();

  // Mobile Menu Toggle
  if (toggleBtn && overlay) {
    let isOpen = false;

    function openMenu() {
      isOpen = true;
      overlay.classList.add('is-open');
      toggleBtn.classList.add('is-active');
      toggleBtn.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';

      // GSAP stagger in links if GSAP is loaded
      if (window.gsap) {
        window.gsap.fromTo(
          '.mobile-menu-link',
          { y: 30, opacity: 0 },
          { y: 0, opacity: 1, duration: 0.45, stagger: 0.06, ease: 'power3.out' }
        );
      }
    }

    function closeMenu() {
      isOpen = false;
      overlay.classList.remove('is-open');
      toggleBtn.classList.remove('is-active');
      toggleBtn.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }

    toggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      if (isOpen) closeMenu();
      else openMenu();
    });

    overlayLinks.forEach((link) => {
      link.addEventListener('click', () => {
        // If clicking on services link when accordion is closed, let it toggle or navigate
        setTimeout(closeMenu, 150);
      });
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && isOpen) {
        closeMenu();
      }
    });

    // Mobile Services Accordion Toggle
    const accordionItem = document.getElementById('mobileServicesAccordion');
    const accordionToggleBtn = document.getElementById('mobileServicesToggleBtn');

    if (accordionItem && accordionToggleBtn) {
      accordionToggleBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        accordionItem.classList.toggle('is-expanded');
      });
    }
  }

  // Desktop Dropdown Hover Persistence
  const dropdownItem = document.querySelector('.nav-item-dropdown');
  const dropdownMenu = document.querySelector('.nav-dropdown-menu');
  if (dropdownItem && dropdownMenu) {
    let leaveTimer = null;
    dropdownItem.addEventListener('mouseenter', () => {
      clearTimeout(leaveTimer);
      dropdownMenu.style.opacity = '1';
      dropdownMenu.style.visibility = 'visible';
      dropdownMenu.style.pointerEvents = 'auto';
      dropdownMenu.style.transform = 'translateY(0)';
    });

    dropdownItem.addEventListener('mouseleave', () => {
      leaveTimer = setTimeout(() => {
        dropdownMenu.style.opacity = '';
        dropdownMenu.style.visibility = '';
        dropdownMenu.style.pointerEvents = '';
        dropdownMenu.style.transform = '';
      }, 150);
    });
  }
}
