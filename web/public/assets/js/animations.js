// THE DRIVE CLINIC — GSAP + ScrollTrigger + Lenis Animation Controller

export function initAnimations() {
  const isReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Initialize Lenis Smooth Scroll (disabled on reduced motion)
  let lenis = null;
  if (!isReducedMotion && window.Lenis) {
    lenis = new window.Lenis({
      duration: 1.2,
      easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
      smoothWheel: true,
      wheelMultiplier: 1.0,
      touchMultiplier: 1.5
    });

    function raf(time) {
      lenis.raf(time);
      requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);

    if (window.gsap && window.ScrollTrigger) {
      lenis.on('scroll', window.ScrollTrigger.update);
      window.gsap.ticker.add((time) => {
        lenis.raf(time * 1000);
      });
      window.gsap.ticker.lagSmoothing(0);
    }
  }

  // Register GSAP Plugins
  if (window.gsap && window.ScrollTrigger) {
    window.gsap.registerPlugin(window.ScrollTrigger);

    if (isReducedMotion) {
      document.querySelectorAll('[data-anim]').forEach((el) => {
        el.classList.add('is-revealed');
      });
      return;
    }

    // 1. Text Reveal Animation
    document.querySelectorAll('[data-anim="text-reveal"]').forEach((el) => {
      window.ScrollTrigger.create({
        trigger: el,
        start: 'top 88%',
        onEnter: () => el.classList.add('is-revealed'),
        once: true
      });
    });

    // 2. Mask Reveal Animation
    document.querySelectorAll('[data-anim="mask-reveal"]').forEach((el) => {
      window.ScrollTrigger.create({
        trigger: el,
        start: 'top 85%',
        onEnter: () => el.classList.add('is-revealed'),
        once: true
      });
    });

    // 3. Redline Scale Animation
    document.querySelectorAll('[data-anim="redline"]').forEach((el) => {
      window.ScrollTrigger.create({
        trigger: el,
        start: 'top 90%',
        onEnter: () => el.classList.add('is-revealed'),
        once: true
      });
    });

    // 4. Fade Up Animation
    document.querySelectorAll('[data-anim="fade-up"]').forEach((el) => {
      window.ScrollTrigger.create({
        trigger: el,
        start: 'top 88%',
        onEnter: () => el.classList.add('is-revealed'),
        once: true
      });
    });

    // 5. Parallax Image Animation
    document.querySelectorAll('[data-anim="parallax"]').forEach((el) => {
      window.gsap.to(el, {
        yPercent: -15,
        ease: 'none',
        scrollTrigger: {
          trigger: el,
          start: 'top bottom',
          end: 'bottom top',
          scrub: 1
        }
      });
    });

    // 6. Numbers / Counters
    document.querySelectorAll('[data-anim="count"]').forEach((el) => {
      const targetVal = parseInt(el.textContent, 10) || 0;
      window.ScrollTrigger.create({
        trigger: el,
        start: 'top 85%',
        onEnter: () => {
          let countObj = { val: 0 };
          window.gsap.to(countObj, {
            val: targetVal,
            duration: 2,
            ease: 'power2.out',
            onUpdate: () => {
              el.textContent = String(Math.floor(countObj.val)).padStart(2, '0');
            }
          });
        },
        once: true
      });
    });
  } else {
    // Fallback if GSAP is not yet loaded from CDN
    document.querySelectorAll('[data-anim]').forEach((el) => {
      el.classList.add('is-revealed');
    });
  }
}
