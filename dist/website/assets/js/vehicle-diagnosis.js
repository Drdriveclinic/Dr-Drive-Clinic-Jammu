// THE DRIVE CLINIC — Interactive Vehicle Diagnosis Recommendation Engine

export function initVehicleDiagnosis() {
  const widget = document.querySelector('.diagnosis-widget');
  if (!widget) return;

  const optionBtns = widget.querySelectorAll('.diagnosis-option-btn');
  const serviceTitleEl = widget.querySelector('.diagnosis-result-title');
  const serviceWhyEl = widget.querySelector('.diagnosis-result-why');
  const ctaBtn = widget.querySelector('.diagnosis-cta-btn');

  const recommendations = {
    'protect-paint': {
      slug: 'ppf',
      name: 'PAINT PROTECTION FILM (PPF)',
      why: 'Self-healing aliphatic polyurethane creates an impenetrable armor against rock chips, scratches, and UV oxidation.'
    },
    'remove-dirt': {
      slug: 'ceramic-wash',
      name: 'CERAMIC WASH',
      why: 'pH-neutral SiO2 foam bath safely lifts road grime while applying high-gloss hydrophobic water beading.'
    },
    'refresh-interior': {
      slug: 'steam-wash',
      name: 'STEAM WASH & SANITIZATION',
      why: '160°C dry vapor steam eliminates 99.9% of bacteria and allergens while deep-cleaning AC vents and cabin fabrics.'
    },
    'detail-bike': {
      slug: 'bike-detailing',
      name: 'PRECISION BIKE DETAILING',
      why: 'Specialized degreasing, chrome restoration, chain maintenance, and paint correction tailored for two-wheelers.'
    },
    'basic-wash': {
      slug: 'basic-car-wash',
      name: 'BASIC CAR WASH',
      why: 'Thorough two-bucket scratch-free foam wash, wheel face clean, and full cabin vacuuming for everyday driving.'
    },
    'full-transformation': {
      slug: 'full-car-detailing',
      name: 'FULL CAR DETAILING',
      why: 'Comprehensive bumper-to-bumper reset combining multi-stage paint correction with deep interior extraction.'
    }
  };

  optionBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      const key = btn.dataset.key;
      const rec = recommendations[key];
      if (!rec) return;

      optionBtns.forEach((b) => b.classList.remove('is-selected'));
      btn.classList.add('is-selected');

      if (serviceTitleEl) {
        serviceTitleEl.textContent = rec.name;
      }
      if (serviceWhyEl) {
        serviceWhyEl.textContent = rec.why;
      }
      if (ctaBtn) {
        ctaBtn.href = `/book?service=${rec.slug}&source=diagnosis`;
      }
    });
  });

  // Default selection
  const defaultBtn = widget.querySelector('.diagnosis-option-btn.is-selected') || optionBtns[0];
  if (defaultBtn) {
    defaultBtn.click();
  }
}
