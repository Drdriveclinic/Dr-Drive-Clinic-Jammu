// THE DRIVE CLINIC — 25-Minute Signature Wash Countdown & Timeline Progress

export function initWashTimer() {
  const section = document.querySelector('.signature-wash-section');
  if (!section) return;

  const timerEl = section.querySelector('.wash-timer-display');
  const progressBar = section.querySelector('.timeline-progress-bar');
  const stepNodes = section.querySelectorAll('.timeline-step-node');

  let totalSeconds = 25 * 60; // 25:00
  let timerInterval = null;
  let isCounting = false;

  function updateDisplay() {
    if (!timerEl) return;
    const mins = Math.floor(totalSeconds / 60);
    const secs = totalSeconds % 60;
    timerEl.textContent = `00:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
  }

  function startTimer() {
    if (isCounting) return;
    isCounting = true;
    timerInterval = setInterval(() => {
      if (totalSeconds > 0) {
        totalSeconds--;
        updateDisplay();
      } else {
        clearInterval(timerInterval);
      }
    }, 1000);
  }

  function pauseTimer() {
    isCounting = false;
    if (timerInterval) clearInterval(timerInterval);
  }

  // Intersection Observer / ScrollTrigger to start countdown when in viewport
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          startTimer();
        } else {
          pauseTimer();
        }
      });
    }, { threshold: 0.3 });
    observer.observe(section);
  } else {
    startTimer();
  }

  // ScrollTrigger for 7-step timeline progress line
  if (window.gsap && window.ScrollTrigger && progressBar) {
    window.ScrollTrigger.create({
      trigger: '.signature-wash-timeline',
      start: 'top 75%',
      end: 'bottom 45%',
      scrub: 0.5,
      onUpdate: (self) => {
        const progress = self.progress;
        progressBar.style.width = `${progress * 100}%`;

        stepNodes.forEach((node, idx) => {
          const threshold = idx / (stepNodes.length - 1);
          if (progress >= threshold) {
            node.classList.add('is-completed');
          } else {
            node.classList.remove('is-completed');
          }
        });
      }
    });
  }
}
