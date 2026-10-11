// THE DRIVE CLINIC — Desktop Dynamic Label Ring Cursor

export function initCursor() {
  if (window.matchMedia('(pointer: coarse)').matches) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  const cursor = document.createElement('div');
  cursor.className = 'custom-cursor';
  cursor.innerHTML = '<span class="cursor-label"></span>';
  document.body.appendChild(cursor);

  const labelEl = cursor.querySelector('.cursor-label');
  let mouseX = window.innerWidth / 2;
  let mouseY = window.innerHeight / 2;
  let cursorX = mouseX;
  let cursorY = mouseY;

  window.addEventListener('mousemove', (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
  }, { passive: true });

  function render() {
    cursorX += (mouseX - cursorX) * 0.2;
    cursorY += (mouseY - cursorY) * 0.2;
    cursor.style.transform = `translate3d(${cursorX}px, ${cursorY}px, 0) translate(-50%, -50%)`;
    requestAnimationFrame(render);
  }
  requestAnimationFrame(render);

  // Dynamic context triggers
  function setCursorState(label) {
    if (label) {
      cursor.classList.add('cursor-active');
      labelEl.textContent = label;
    } else {
      cursor.classList.remove('cursor-active');
      labelEl.textContent = '';
    }
  }

  document.addEventListener('mouseover', (e) => {
    const target = e.target.closest('[data-cursor], a, button, .service-card, .gallery-item, .before-after-container');
    if (!target) {
      setCursorState(null);
      return;
    }

    if (target.dataset.cursor) {
      setCursorState(target.dataset.cursor);
    } else if (target.closest('.before-after-container')) {
      setCursorState('DRAG');
    } else if (target.closest('.gallery-item') || target.tagName === 'IMG') {
      setCursorState('VIEW');
    } else if (target.closest('.service-card') || target.closest('.service-item')) {
      setCursorState('EXPLORE');
    } else if (target.tagName === 'BUTTON' || target.classList.contains('btn')) {
      setCursorState('CLICK');
    } else {
      setCursorState(null);
    }
  });

  document.addEventListener('mouseleave', () => {
    cursor.style.opacity = '0';
  });

  document.addEventListener('mouseenter', () => {
    cursor.style.opacity = '1';
  });
}
