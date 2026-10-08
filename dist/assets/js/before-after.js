// THE DRIVE CLINIC — Before / After Drag Slider Component & Category Switcher

export function initBeforeAfter() {
  const containers = document.querySelectorAll('.before-after-container');

  containers.forEach((container) => {
    const rangeInput = container.querySelector('.before-after-input');
    const afterLayer = container.querySelector('.before-after-layer-after');
    const divider = container.querySelector('.before-after-divider');

    if (!rangeInput || !afterLayer || !divider) return;

    function updateSlider(val) {
      afterLayer.style.clipPath = `inset(0 0 0 ${val}%)`;
      divider.style.left = `${val}%`;
    }

    rangeInput.addEventListener('input', (e) => {
      updateSlider(e.target.value);
    });

    // Touch & mouse drag support
    updateSlider(rangeInput.value || 50);
  });

  // Category switcher chips on homepage
  const section = document.querySelector('.before-after-section');
  if (!section) return;

  const chips = section.querySelectorAll('.ba-category-chip');
  const pairs = section.querySelectorAll('.ba-pair-item');
  const emptyState = section.querySelector('.ba-empty-state');

  chips.forEach((chip) => {
    chip.addEventListener('click', () => {
      const category = chip.dataset.category;

      chips.forEach((c) => c.classList.remove('is-active'));
      chip.classList.add('is-active');

      let matchFound = false;
      pairs.forEach((pair) => {
        if (pair.dataset.category === category) {
          pair.style.display = 'block';
          matchFound = true;
        } else {
          pair.style.display = 'none';
        }
      });

      if (emptyState) {
        emptyState.style.display = matchFound ? 'none' : 'block';
      }
    });
  });
}
