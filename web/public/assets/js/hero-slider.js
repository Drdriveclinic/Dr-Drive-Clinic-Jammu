// THE DRIVE CLINIC — Hero Slider Component
// 5 slides, clip-path reveals, Ken Burns scale, progress bars, swipe & keyboard navigation

export function initHeroSlider() {
  const slider = document.querySelector('.hero-slider');
  if (!slider) return;

  const slides = slider.querySelectorAll('.hero-slide');
  const progressBars = slider.querySelectorAll('.hero-progress-fill');
  const currentCountEl = slider.querySelector('.hero-counter-current');
  const prevBtn = slider.querySelector('.hero-arrow-prev');
  const nextBtn = slider.querySelector('.hero-arrow-next');

  if (slides.length === 0) return;

  let currentIndex = 0;
  let timer = null;
  const slideDuration = 6500; // 6.5s per slide
  let isPaused = false;
  let startX = 0;
  let endX = 0;

  function setSlide(index, direction = 'next') {
    if (index < 0) index = slides.length - 1;
    if (index >= slides.length) index = 0;

    const prevSlide = slides[currentIndex];
    const nextSlide = slides[index];

    // Reset progress bars
    progressBars.forEach((bar, i) => {
      bar.style.transition = 'none';
      bar.style.width = i < index ? '100%' : '0%';
    });

    // Update ARIA and active classes
    slides.forEach((s, i) => {
      s.classList.remove('is-active', 'is-entering', 'is-exiting');
      s.setAttribute('aria-hidden', i === index ? 'false' : 'true');
    });

    nextSlide.classList.add('is-active', 'is-entering');
    if (prevSlide && prevSlide !== nextSlide) {
      prevSlide.classList.add('is-exiting');
    }

    // Trigger clip-path reveal & GSAP text reveal
    if (window.gsap) {
      // Headline lines stagger up from translateY(105%)
      const lines = nextSlide.querySelectorAll('.hero-headline-inner');
      window.gsap.fromTo(
        lines,
        { yPercent: 105 },
        { yPercent: 0, duration: 0.8, stagger: 0.13, ease: 'power3.out', delay: 0.2 }
      );

      // Label, supporting, CTA fade up
      const metaElements = nextSlide.querySelectorAll('.hero-label, .hero-supporting, .hero-ctas');
      window.gsap.fromTo(
        metaElements,
        { opacity: 0, y: 24 },
        { opacity: 1, y: 0, duration: 0.7, stagger: 0.1, ease: 'power2.out', delay: 0.4 }
      );
    }

    // Update Counter
    if (currentCountEl) {
      currentCountEl.textContent = String(index + 1).padStart(2, '0');
    }

    currentIndex = index;

    // Start progress bar animation
    const activeBar = progressBars[currentIndex];
    if (activeBar) {
      // Force repaint
      activeBar.getBoundingClientRect();
      activeBar.style.transition = `width ${slideDuration}ms linear`;
      activeBar.style.width = '100%';
    }

    resetTimer();
  }

  function nextSlide() {
    setSlide(currentIndex + 1, 'next');
  }

  function prevSlide() {
    setSlide(currentIndex - 1, 'prev');
  }

  function resetTimer() {
    if (timer) clearInterval(timer);
    const isReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!isPaused && !isReducedMotion) {
      timer = setInterval(nextSlide, slideDuration);
    }
  }

  // Event Listeners
  if (prevBtn) prevBtn.addEventListener('click', prevSlide);
  if (nextBtn) nextBtn.addEventListener('click', nextSlide);

  // Keyboard navigation when slider has focus
  slider.setAttribute('tabindex', '0');
  slider.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight') {
      nextSlide();
      e.preventDefault();
    } else if (e.key === 'ArrowLeft') {
      prevSlide();
      e.preventDefault();
    }
  });

  // Hover / Focus pause
  slider.addEventListener('mouseenter', () => {
    isPaused = true;
    if (timer) clearInterval(timer);
  });

  slider.addEventListener('mouseleave', () => {
    isPaused = false;
    resetTimer();
  });

  // Touch Swipe
  slider.addEventListener('touchstart', (e) => {
    startX = e.touches[0].clientX;
  }, { passive: true });

  slider.addEventListener('touchend', (e) => {
    endX = e.changedTouches[0].clientX;
    const diff = startX - endX;
    if (Math.abs(diff) > 40) {
      if (diff > 0) nextSlide();
      else prevSlide();
    }
  }, { passive: true });

  // Initialize First Slide
  setSlide(0);
}
