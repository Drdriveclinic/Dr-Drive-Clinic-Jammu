// THE DRIVE CLINIC — Dynamic Cinema Hero Text Slider
// Ultra-clean kinetic text transitions over persistent background video with interactive navigation dock

export function initHeroSlider() {
  const cinemaHero = document.querySelector('.hero-cinema');
  if (cinemaHero) {
    initDynamicCinemaSlider(cinemaHero);
    return;
  }

  // Fallback for legacy hero-slider if present
  const slider = document.querySelector('.hero-slider');
  if (!slider) return;

  const slides = slider.querySelectorAll('.hero-slide');
  const progressBars = slider.querySelectorAll('.hero-progress-fill');
  if (slides.length === 0) return;

  let currentIndex = 0;
  function setSlide(index) {
    if (index < 0) index = slides.length - 1;
    if (index >= slides.length) index = 0;

    slides.forEach((s, i) => {
      s.classList.toggle('is-active', i === index);
      s.setAttribute('aria-hidden', i === index ? 'false' : 'true');
    });

    progressBars.forEach((bar, i) => {
      bar.style.width = i < index ? '100%' : '0%';
    });

    currentIndex = index;
  }

  const prevBtn = slider.querySelector('.hero-arrow-prev');
  const nextBtn = slider.querySelector('.hero-arrow-next');
  if (prevBtn) prevBtn.addEventListener('click', () => setSlide(currentIndex - 1));
  if (nextBtn) nextBtn.addEventListener('click', () => setSlide(currentIndex + 1));
}

function initDynamicCinemaSlider(hero) {
  // Read slides data from embedded JSON script tag
  const dataEl = document.getElementById('heroSlidesData');
  let slidesData = [];
  try {
    if (dataEl && dataEl.textContent.trim()) {
      slidesData = JSON.parse(dataEl.textContent.trim());
    }
  } catch (err) {
    console.warn('[Hero] Failed to parse slides JSON:', err);
  }

  // Fallback slide definitions if JSON is unavailable
  if (!slidesData || slidesData.length === 0) {
    slidesData = [
      {
        tag: 'PPF Armor',
        badge: '01 / PAINT PROTECTION FILM',
        lead_keyword: 'SELF-HEALING PPF ARMOR.',
        supporting: "Instant self-healing TPU armor defending original factory paint against highway gravel, micro-scratches, and Jammu's scorching UV sun.",
        service_slug: 'ppf',
        cta_label: 'EXPLORE PPF SPECS →'
      },
      {
        tag: '9H Ceramic',
        badge: '02 / 9H NANO CERAMIC COATING',
        lead_keyword: '9H CERAMIC GLASS MATRIX.',
        supporting: 'Nanotechnology liquid quartz bonding permanently to clear coat for intense candy reflections, chemical resistance, and water repellency.',
        service_slug: 'ceramic-coating',
        cta_label: 'EXPLORE 9H CERAMIC →'
      },
      {
        tag: 'Full Detailing',
        badge: '03 / FULL CAR RESTORATION',
        lead_keyword: 'COMPLETE CAR RESET.',
        supporting: 'Two-stage machine paint correction, dry steam interior sterilization, leather feeding, and engine bay detailing for total vehicle rejuvenation.',
        service_slug: 'full-car-detailing',
        cta_label: 'VIEW DETAILING SPECS →'
      },
      {
        tag: 'Studio Wash',
        badge: '04 / CERAMIC & STEAM WASH',
        lead_keyword: 'SWIRL-FREE STUDIO WASH.',
        supporting: 'Dual-bucket grit-guarded washes with SiO2 ceramic gloss boosters, spot-free filtered water, and hospital-grade interior steam sanitization.',
        service_slug: 'ceramic-wash',
        cta_label: 'VIEW WASH PROGRAMS →'
      },
      {
        tag: 'Superbikes',
        badge: '05 / BIKE & SUPERBIKE CARE',
        lead_keyword: 'PRECISION TWO-WHEEL CARE.',
        supporting: 'Bespoke precision detailing tailored for superbikes and performance machines — engine fin degreasing, fairing PPF, and ceramic shields.',
        service_slug: 'bike-detailing',
        cta_label: 'EXPLORE BIKE CARE →'
      }
    ];
  }

  // Target DOM elements
  const heroBadge = document.getElementById('heroBadge');
  const heroDynamicTitle = document.getElementById('heroDynamicTitle');
  const heroDynamicDesc = document.getElementById('heroDynamicDesc');
  const heroBookBtn = document.getElementById('heroBookBtn');
  const heroDetailBtn = document.getElementById('heroDetailBtn');
  const heroDockCurrent = document.getElementById('heroDockCurrent');
  const dockBtns = hero.querySelectorAll('.hero-dock-btn');
  const prevBtn = document.getElementById('heroPrevBtn');
  const nextBtn = document.getElementById('heroNextBtn');

  let currentIndex = 0;
  let progressAnim = null;
  const slideDuration = 5200; // 5.2 seconds per slide
  let isPaused = false;
  let startTime = null;
  let isTransitioning = false;

  function goToSlide(index) {
    if (isTransitioning) return;
    if (index < 0) index = slidesData.length - 1;
    if (index >= slidesData.length) index = 0;

    const data = slidesData[index];
    if (!data) return;

    isTransitioning = true;
    stopProgress();

    // 1. Update counter
    if (heroDockCurrent) {
      heroDockCurrent.textContent = String(index + 1).padStart(2, '0');
    }

    // 2. Update Dock Tabs & Bars
    dockBtns.forEach((btn, i) => {
      const isActive = i === index;
      btn.classList.toggle('is-active', isActive);
      btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
      const fill = btn.querySelector('.dock-btn-fill');
      if (fill) {
        fill.style.width = i < index ? '100%' : '0%';
      }
    });

    // 3. Smooth Kinetic Text Transition
    const animTargets = [heroBadge, heroDynamicTitle, heroDynamicDesc, heroDetailBtn, heroBookBtn].filter(Boolean);

    if (window.gsap) {
      window.gsap.to(animTargets, {
        opacity: 0,
        y: -10,
        duration: 0.22,
        stagger: 0.02,
        ease: 'power2.in',
        onComplete: () => {
          updateDOMValues(data);
          window.gsap.fromTo(
            animTargets,
            { opacity: 0, y: 12 },
            {
              opacity: 1,
              y: 0,
              duration: 0.42,
              stagger: 0.05,
              ease: 'power3.out',
              onComplete: () => {
                isTransitioning = false;
                startProgress();
              }
            }
          );
        }
      });
    } else {
      // Clean CSS fallback transition
      animTargets.forEach((el) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';
      });

      setTimeout(() => {
        updateDOMValues(data);
        animTargets.forEach((el) => {
          el.style.opacity = '1';
          el.style.transform = 'translateY(0)';
        });
        isTransitioning = false;
        startProgress();
      }, 220);
    }

    currentIndex = index;
  }

  function updateDOMValues(data) {
    if (heroBadge) heroBadge.textContent = data.badge;
    if (heroDynamicTitle) heroDynamicTitle.textContent = data.lead_keyword;
    if (heroDynamicDesc) heroDynamicDesc.textContent = data.supporting;
    if (heroBookBtn) heroBookBtn.href = `/book?service=${encodeURIComponent(data.service_slug)}`;
    if (heroDetailBtn) {
      heroDetailBtn.href = `/services/${encodeURIComponent(data.service_slug)}`;
      heroDetailBtn.textContent = data.cta_label;
    }
  }

  function startProgress() {
    stopProgress();
    if (isPaused) return;

    const activeBtn = dockBtns[currentIndex];
    const fill = activeBtn ? activeBtn.querySelector('.dock-btn-fill') : null;
    startTime = performance.now();

    function step(now) {
      if (isPaused) return;
      const elapsed = now - startTime;
      const pct = Math.min(100, (elapsed / slideDuration) * 100);

      if (fill) fill.style.width = pct + '%';

      if (elapsed >= slideDuration) {
        goToSlide(currentIndex + 1);
      } else {
        progressAnim = requestAnimationFrame(step);
      }
    }

    progressAnim = requestAnimationFrame(step);
  }

  function stopProgress() {
    if (progressAnim) {
      cancelAnimationFrame(progressAnim);
      progressAnim = null;
    }
  }

  // Interactive Dock Clicks
  dockBtns.forEach((btn, idx) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      if (idx !== currentIndex) {
        goToSlide(idx);
      }
    });
  });

  // Prev / Next Buttons
  if (prevBtn) {
    prevBtn.addEventListener('click', (e) => {
      e.preventDefault();
      goToSlide(currentIndex - 1);
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', (e) => {
      e.preventDefault();
      goToSlide(currentIndex + 1);
    });
  }

  // Pause on hover over text content or dock
  const interactiveAreas = [hero.querySelector('.hero-cinema-main'), hero.querySelector('.hero-nav-dock')].filter(Boolean);
  interactiveAreas.forEach((area) => {
    area.addEventListener('mouseenter', () => {
      isPaused = true;
      stopProgress();
    });
    area.addEventListener('mouseleave', () => {
      isPaused = false;
      startProgress();
    });
  });

  // Keyboard navigation when hero is in viewport
  window.addEventListener('keydown', (e) => {
    const rect = hero.getBoundingClientRect();
    const inView = rect.top <= window.innerHeight && rect.bottom >= 0;
    if (!inView) return;

    if (e.key === 'ArrowRight') {
      goToSlide(currentIndex + 1);
    } else if (e.key === 'ArrowLeft') {
      goToSlide(currentIndex - 1);
    }
  });

  // Initialize first slide progress
  startProgress();
}
