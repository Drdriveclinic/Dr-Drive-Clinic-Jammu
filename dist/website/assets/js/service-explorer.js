// THE DRIVE CLINIC — Interactive Service Explorer
// Dynamic Preview Image & Title Swapping on Click and Hover

export function initServiceExplorer() {
  const explorer = document.querySelector('.service-explorer');
  if (!explorer) return;

  const tabBtns = explorer.querySelectorAll('.explorer-tab-btn');
  const serviceLists = explorer.querySelectorAll('.explorer-group-list');
  const previewImg = explorer.querySelector('.explorer-preview-img');
  const previewTitle = explorer.querySelector('.explorer-preview-title');

  function updatePreview(item) {
    if (!item) return;
    const allItems = explorer.querySelectorAll('.explorer-service-item');
    allItems.forEach((i) => i.classList.remove('is-hovered'));
    item.classList.add('is-hovered');

    const name = item.dataset.name || item.querySelector('.explorer-service-title')?.textContent?.trim() || '';
    const imgUrl = item.dataset.image;

    if (previewTitle && name) {
      previewTitle.textContent = name;
    }

    if (previewImg && imgUrl) {
      const currentSrc = previewImg.getAttribute('src');
      if (currentSrc !== imgUrl) {
        previewImg.style.opacity = '0.35';
        previewImg.style.transform = 'scale(0.97)';
        setTimeout(() => {
          previewImg.src = imgUrl;
          previewImg.alt = name;
          previewImg.style.opacity = '1';
          previewImg.style.transform = 'scale(1)';
        }, 120);
      }
    }
  }

  // Tab Switching
  tabBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      const category = btn.dataset.category;

      tabBtns.forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');

      serviceLists.forEach((list) => {
        if (list.dataset.category === category) {
          list.classList.add('is-active');
          const firstItem = list.querySelector('.explorer-service-item');
          if (firstItem) {
            updatePreview(firstItem);
            // Open first item accordion
            firstItem.classList.add('is-expanded');
            const body = firstItem.querySelector('.explorer-service-body');
            if (body) body.style.maxHeight = body.scrollHeight + 'px';
          }
        } else {
          list.classList.remove('is-active');
        }
      });
    });
  });

  // Service Item Hover & Click
  const allItems = explorer.querySelectorAll('.explorer-service-item');
  allItems.forEach((item) => {
    // Hover: Swap sticky image on right immediately
    item.addEventListener('mouseenter', () => {
      updatePreview(item);
    });

    // Click: Update image AND toggle accordion expansion
    item.addEventListener('click', (e) => {
      updatePreview(item);

      // Don't intercept click on internal links
      if (e.target.closest('a')) return;

      const isExpanded = item.classList.contains('is-expanded');
      const parentList = item.closest('.explorer-group-list');
      if (parentList) {
        parentList.querySelectorAll('.explorer-service-item').forEach((other) => {
          if (other !== item) {
            other.classList.remove('is-expanded');
            const body = other.querySelector('.explorer-service-body');
            if (body) body.style.maxHeight = '0px';
          }
        });
      }

      if (isExpanded) {
        item.classList.remove('is-expanded');
        const body = item.querySelector('.explorer-service-body');
        if (body) body.style.maxHeight = '0px';
      } else {
        item.classList.add('is-expanded');
        const body = item.querySelector('.explorer-service-body');
        if (body) body.style.maxHeight = body.scrollHeight + 'px';
      }
    });
  });

  // Initialize with first tab active
  const firstActiveItem = explorer.querySelector('.explorer-group-list.is-active .explorer-service-item');
  if (firstActiveItem) {
    updatePreview(firstActiveItem);
    firstActiveItem.classList.add('is-expanded');
    const body = firstActiveItem.querySelector('.explorer-service-body');
    if (body) body.style.maxHeight = body.scrollHeight + 'px';
  }
}
