// THE DRIVE CLINIC — Interactive Service Explorer
// Tabs (Protect / Detail / Clean), hover preview swapping, and expandable details

export function initServiceExplorer() {
  const explorer = document.querySelector('.service-explorer');
  if (!explorer) return;

  const tabBtns = explorer.querySelectorAll('.explorer-tab-btn');
  const serviceLists = explorer.querySelectorAll('.explorer-group-list');
  const previewImg = explorer.querySelector('.explorer-preview-img');
  const previewCaption = explorer.querySelector('.explorer-preview-caption');

  // Tab Switching
  tabBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      const category = btn.dataset.category;

      tabBtns.forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');

      serviceLists.forEach((list) => {
        if (list.dataset.category === category) {
          list.classList.add('is-active');
          // Highlight first item in newly active list
          const firstItem = list.querySelector('.explorer-service-item');
          if (firstItem) updatePreview(firstItem);
        } else {
          list.classList.remove('is-active');
        }
      });
    });
  });

  // Service Item Hover & Click
  const allItems = explorer.querySelectorAll('.explorer-service-item');

  function updatePreview(item) {
    allItems.forEach((i) => i.classList.remove('is-hovered'));
    item.classList.add('is-hovered');

    const name = item.dataset.name || item.querySelector('.explorer-service-title').textContent.trim();
    const caption = item.dataset.caption || `PHOTO — ${name} detailing in progress`;
    
    if (previewCaption) {
      previewCaption.textContent = caption;
    }
  }

  allItems.forEach((item) => {
    // Hover: Swap sticky image on right & shift name 14px
    item.addEventListener('mouseenter', () => {
      updatePreview(item);
    });

    // Click: Toggle accordion expansion
    const header = item.querySelector('.explorer-service-header');
    if (header) {
      header.addEventListener('click', () => {
        const isExpanded = item.classList.contains('is-expanded');
        
        // Collapse other items in current list
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
    }
  });

  // Initialize with first tab active
  const firstActiveItem = explorer.querySelector('.explorer-group-list.is-active .explorer-service-item');
  if (firstActiveItem) {
    updatePreview(firstActiveItem);
  }
}
