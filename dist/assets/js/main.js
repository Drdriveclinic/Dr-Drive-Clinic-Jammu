// THE DRIVE CLINIC — Client-Side Core Entry Point

import { initNavigation } from './navigation.js';
import { initCursor } from './cursor.js';
import { initAnimations } from './animations.js';
import { initPageTransition } from './page-transition.js';
import { initHeroSlider } from './hero-slider.js';
import { initServiceExplorer } from './service-explorer.js';
import { initVehicleHotspots } from './vehicle-hotspots.js';
import { initWashTimer } from './wash-timer.js';
import { initVehicleDiagnosis } from './vehicle-diagnosis.js';
import { initBeforeAfter } from './before-after.js';
import { initBookingFlow } from './booking-flow.js';

document.addEventListener('DOMContentLoaded', () => {
  // Mark JavaScript enabled for CSS reveal system
  document.documentElement.classList.add('js');

  // Initialize Global Modules
  initNavigation();
  initCursor();
  initAnimations();
  initPageTransition();

  // Initialize Interactive Feature Modules
  initHeroSlider();
  initServiceExplorer();
  initVehicleHotspots();
  initWashTimer();
  initVehicleDiagnosis();
  initBeforeAfter();
  initBookingFlow();

  // FAQ Accordion Handler
  document.querySelectorAll('.faq-trigger').forEach((btn) => {
    btn.addEventListener('click', () => {
      const isExpanded = btn.getAttribute('aria-expanded') === 'true';
      const item = btn.closest('.faq-item');
      const content = item.querySelector('.faq-content');

      // Close sibling items in accordion
      const accordion = btn.closest('.faq-accordion');
      if (accordion) {
        accordion.querySelectorAll('.faq-trigger').forEach((otherBtn) => {
          if (otherBtn !== btn) {
            otherBtn.setAttribute('aria-expanded', 'false');
            const otherContent = otherBtn.closest('.faq-item').querySelector('.faq-content');
            if (otherContent) otherContent.style.maxHeight = '0px';
          }
        });
      }

      if (isExpanded) {
        btn.setAttribute('aria-expanded', 'false');
        content.style.maxHeight = '0px';
      } else {
        btn.setAttribute('aria-expanded', 'true');
        content.style.maxHeight = content.scrollHeight + 'px';
      }
    });
  });

  // Cinematic Hero Video Background Autoplay Assurance & Performance Optimization
  const heroVideo = document.querySelector('.hero-bg-video');
  if (heroVideo) {
    const playPromise = heroVideo.play();
    if (playPromise !== undefined) {
      playPromise.catch(() => {
        const startOnInteract = () => {
          heroVideo.play().catch(() => {});
        };
        window.addEventListener('click', startOnInteract, { once: true });
        window.addEventListener('touchstart', startOnInteract, { once: true });
      });
    }

    if ('IntersectionObserver' in window) {
      const heroObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            heroVideo.play().catch(() => {});
          } else {
            heroVideo.pause();
          }
        });
      }, { threshold: 0.1 });
      const heroSection = document.querySelector('.hero-cinema');
      if (heroSection) heroObserver.observe(heroSection);
    }
  }
});
