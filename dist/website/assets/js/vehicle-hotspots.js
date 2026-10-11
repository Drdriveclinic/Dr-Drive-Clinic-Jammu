// THE DRIVE CLINIC — Interactive PPF Vehicle Hotspot Coordinator

export function initVehicleHotspots() {
  const container = document.querySelector('.ppf-hotspot-widget');
  if (!container) return;

  const hotspots = container.querySelectorAll('.hotspot-btn');
  const zoneCards = container.querySelectorAll('.hotspot-zone-card');
  const svgHighlights = container.querySelectorAll('.svg-zone-highlight');

  const zoneDetails = {
    hood: {
      name: 'HOOD / BONNET',
      reason: 'Takes 65% of high-speed airborne gravel, bug spatter, and sun heat directly from front impact.'
    },
    bumper: {
      name: 'FRONT BUMPER',
      reason: 'First point of contact for stone chips, road grit, high curbs, and bumper scuffs.'
    },
    fender: {
      name: 'FRONT FENDERS',
      reason: 'Guards against tire sling, debris kick-up, and side road dust.'
    },
    orvm: {
      name: 'SIDE MIRRORS (ORVM)',
      reason: 'Highly aerodynamic projection vulnerable to tight city scraping and insect etching.'
    },
    headlight: {
      name: 'HEADLIGHTS',
      reason: 'Prevents polycarbonate clouding, yellowing, and high-speed rock pitting.'
    }
  };

  function selectZone(zoneKey) {
    if (!zoneKey || !zoneDetails[zoneKey]) zoneKey = 'hood';

    // Update Hotspot buttons
    hotspots.forEach((btn) => {
      const active = btn.dataset.zone === zoneKey;
      btn.classList.toggle('is-active', active);
      btn.setAttribute('aria-pressed', active ? 'true' : 'false');
    });

    // Update bottom zone cards
    zoneCards.forEach((card) => {
      const active = card.dataset.zone === zoneKey;
      card.classList.toggle('is-active', active);
    });

    // Update SVG highlighted zones
    svgHighlights.forEach((el) => {
      const active = el.dataset.zone === zoneKey;
      el.classList.toggle('is-active', active);
    });
  }

  // Click on hotspot buttons
  hotspots.forEach((btn) => {
    btn.addEventListener('click', () => {
      selectZone(btn.dataset.zone);
    });
  });

  // Click on zone cards below
  zoneCards.forEach((card) => {
    card.addEventListener('click', () => {
      selectZone(card.dataset.zone);
    });
  });

  // Initialize with 'hood'
  selectZone('hood');
}
