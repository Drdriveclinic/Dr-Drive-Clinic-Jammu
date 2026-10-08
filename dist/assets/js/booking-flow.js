// THE DRIVE CLINIC — 7-Step Interactive Booking Engine

export function initBookingFlow() {
  const container = document.querySelector('.booking-flow-container');
  if (!container) return;

  const API_BASE = window.THE_DRIVE_CLINIC_CONFIG?.apiBaseUrl || 'http://localhost:5000/api';

  // State
  let state = {
    step: 1,
    vehicleType: 'car',
    serviceSlug: container.dataset.preselectedService || '',
    serviceName: '',
    serviceDuration: '',
    brand: '',
    model: '',
    year: '',
    registration: '',
    color: '',
    date: '',
    dateDisplay: '',
    time: '',
    timeDisplay: '',
    name: '',
    phone: '',
    whatsapp: '',
    whatsappSameAsPhone: true,
    email: '',
    notes: '',
    isRecurring: container.dataset.recurring === '1',
    consent: false
  };

  // Restore State from sessionStorage (try/catch protected)
  try {
    const saved = sessionStorage.getItem('tdc_booking_state');
    if (saved) {
      const parsed = JSON.parse(saved);
      state = { ...state, ...parsed };
      // Always retain query param preselection if present
      if (container.dataset.preselectedService) {
        state.serviceSlug = container.dataset.preselectedService;
      }
    }
  } catch (e) {
    console.warn('[SessionStorage Warning]:', e);
  }

  function saveState() {
    try {
      sessionStorage.setItem('tdc_booking_state', JSON.stringify(state));
    } catch (e) {}
  }

  // DOM Elements
  const stepPanels = container.querySelectorAll('.booking-step-panel');
  const prevBtn = container.querySelector('.booking-prev-btn');
  const nextBtn = container.querySelector('.booking-next-btn');
  const submitBtn = container.querySelector('.booking-submit-btn');
  const progressSegments = container.querySelectorAll('.booking-progress-segment');
  const stepCounterEl = container.querySelector('.booking-step-counter');
  const stepNameEl = container.querySelector('.booking-step-name');
  const summaryList = container.querySelector('.booking-summary-list');
  const sidebarPhotoCaption = container.querySelector('.booking-sidebar-photo-caption');
  const errorMessageEl = container.querySelector('.booking-error-banner');

  const stepNames = [
    'VEHICLE TYPE',
    'SELECT SERVICE',
    'VEHICLE DETAILS',
    'SELECT DATE',
    'SELECT TIME',
    'CONTACT DETAILS',
    'CONFIRMATION'
  ];

  // Load Services from API / DOM
  const serviceCards = container.querySelectorAll('.booking-service-card');

  function updateSidebar() {
    if (sidebarPhotoCaption) {
      sidebarPhotoCaption.textContent = state.vehicleType === 'bike'
        ? 'PHOTO — Motorcycle Precision Detailing Bay'
        : 'PHOTO — Premium Automotive Clean Bay & PPF Studio';
    }

    if (summaryList) {
      summaryList.innerHTML = `
        <div class="summary-row">
          <span class="summary-label">VEHICLE TYPE</span>
          <span class="summary-val">${state.vehicleType.toUpperCase()}</span>
        </div>
        ${state.serviceName ? `
          <div class="summary-row">
            <span class="summary-label">SERVICE</span>
            <span class="summary-val text-red">${state.serviceName}</span>
          </div>
        ` : ''}
        ${state.brand && state.model ? `
          <div class="summary-row">
            <span class="summary-label">VEHICLE</span>
            <span class="summary-val">${state.brand} ${state.model} ${state.year ? `(${state.year})` : ''}</span>
          </div>
        ` : ''}
        ${state.registration ? `
          <div class="summary-row">
            <span class="summary-label">REGISTRATION</span>
            <span class="summary-val">${state.registration}</span>
          </div>
        ` : ''}
        ${state.dateDisplay ? `
          <div class="summary-row">
            <span class="summary-label">DATE</span>
            <span class="summary-val">${state.dateDisplay}</span>
          </div>
        ` : ''}
        ${state.timeDisplay ? `
          <div class="summary-row">
            <span class="summary-label">TIME</span>
            <span class="summary-val">${state.timeDisplay}</span>
          </div>
        ` : ''}
        ${state.name ? `
          <div class="summary-row">
            <span class="summary-label">CLIENT</span>
            <span class="summary-val">${state.name}</span>
          </div>
        ` : ''}
      `;
    }
  }

  function validateStep(stepNum) {
    if (stepNum === 1) return !!state.vehicleType;
    if (stepNum === 2) return !!state.serviceSlug;
    if (stepNum === 3) return !!state.brand && !!state.model;
    if (stepNum === 4) return !!state.date;
    if (stepNum === 5) return !!state.time;
    if (stepNum === 6) {
      const phoneValid = /^[6-9]\d{9}$/.test(state.phone);
      return state.name.length >= 2 && phoneValid;
    }
    if (stepNum === 7) return state.consent === true;
    return true;
  }

  function updateButtons() {
    const isValid = validateStep(state.step);

    if (prevBtn) {
      prevBtn.style.visibility = state.step > 1 ? 'visible' : 'hidden';
    }

    if (nextBtn && submitBtn) {
      if (state.step === 7) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-flex';
        submitBtn.disabled = !isValid;
      } else {
        nextBtn.style.display = 'inline-flex';
        submitBtn.style.display = 'none';
        nextBtn.disabled = !isValid;
      }
    }

    // Top progress segments
    progressSegments.forEach((seg, idx) => {
      seg.classList.toggle('is-active', idx + 1 <= state.step);
    });

    if (stepCounterEl) {
      stepCounterEl.textContent = `STEP 0${state.step} / 07`;
    }
    if (stepNameEl) {
      stepNameEl.textContent = `· ${stepNames[state.step - 1]}`;
    }
  }

  function goToStep(stepNum) {
    if (stepNum < 1) stepNum = 1;
    if (stepNum > 7) stepNum = 7;

    state.step = stepNum;
    saveState();

    stepPanels.forEach((panel) => {
      const pStep = parseInt(panel.dataset.step, 10);
      if (pStep === state.step) {
        panel.style.display = 'block';
        if (window.gsap) {
          window.gsap.fromTo(panel, { opacity: 0, y: 16 }, { opacity: 1, y: 0, duration: 0.35, ease: 'power2.out' });
        }
      } else {
        panel.style.display = 'none';
      }
    });

    if (state.step === 2) filterServicesByType();
    if (state.step === 4) loadCalendarDays();
    if (state.step === 5) loadTimeSlots();
    if (state.step === 7) renderConfirmSummary();

    updateSidebar();
    updateButtons();
    hideError();
  }

  function showError(msg) {
    if (errorMessageEl) {
      errorMessageEl.textContent = msg;
      errorMessageEl.style.display = 'block';
    }
  }

  function hideError() {
    if (errorMessageEl) {
      errorMessageEl.style.display = 'none';
    }
  }

  // -------------------------------------------------------------
  // STEP 1: VEHICLE TYPE
  // -------------------------------------------------------------
  const typeTiles = container.querySelectorAll('.type-tile');
  typeTiles.forEach((tile) => {
    tile.addEventListener('click', () => {
      typeTiles.forEach((t) => t.classList.remove('is-selected'));
      tile.classList.add('is-selected');
      state.vehicleType = tile.dataset.type;
      
      // If selected service doesn't match new vehicle type, clear it
      filterServicesByType();
      updateSidebar();
      updateButtons();
    });
  });

  // -------------------------------------------------------------
  // STEP 2: SERVICE FILTER
  // -------------------------------------------------------------
  function filterServicesByType() {
    serviceCards.forEach((card) => {
      const types = card.dataset.vehicleTypes || 'car,bike';
      const matches = types.includes(state.vehicleType);
      card.style.display = matches ? 'block' : 'none';

      if (card.dataset.slug === state.serviceSlug && matches) {
        card.classList.add('is-selected');
        state.serviceName = card.dataset.name;
        state.serviceDuration = card.dataset.duration;
      } else if (!matches && card.dataset.slug === state.serviceSlug) {
        card.classList.remove('is-selected');
        state.serviceSlug = '';
        state.serviceName = '';
      }
    });
  }

  serviceCards.forEach((card) => {
    card.addEventListener('click', () => {
      serviceCards.forEach((c) => c.classList.remove('is-selected'));
      card.classList.add('is-selected');
      state.serviceSlug = card.dataset.slug;
      state.serviceName = card.dataset.name;
      state.serviceDuration = card.dataset.duration;
      updateSidebar();
      updateButtons();
    });
  });

  // -------------------------------------------------------------
  // STEP 3: VEHICLE DETAILS
  // -------------------------------------------------------------
  const brandInput = container.querySelector('input[name="brand"]');
  const modelInput = container.querySelector('input[name="model"]');
  const yearInput = container.querySelector('input[name="year"]');
  const regInput = container.querySelector('input[name="registration"]');
  const colorInput = container.querySelector('input[name="color"]');

  if (brandInput) {
    brandInput.value = state.brand;
    brandInput.addEventListener('input', (e) => {
      state.brand = e.target.value.trim();
      updateSidebar();
      updateButtons();
    });
  }

  if (modelInput) {
    modelInput.value = state.model;
    modelInput.addEventListener('input', (e) => {
      state.model = e.target.value.trim();
      updateSidebar();
      updateButtons();
    });
  }

  if (yearInput) {
    yearInput.value = state.year;
    yearInput.addEventListener('input', (e) => {
      state.year = e.target.value.trim();
      updateSidebar();
      updateButtons();
    });
  }

  if (regInput) {
    regInput.value = state.registration;
    // Format Registration format e.g. JK 02 AB 1234
    regInput.addEventListener('input', (e) => {
      state.registration = e.target.value.toUpperCase();
      updateSidebar();
      updateButtons();
    });
  }

  if (colorInput) {
    colorInput.value = state.color;
    colorInput.addEventListener('input', (e) => {
      state.color = e.target.value.trim();
      updateSidebar();
      updateButtons();
    });
  }

  // -------------------------------------------------------------
  // STEP 4: DATE PICKER (14 Days)
  // -------------------------------------------------------------
  async function loadCalendarDays() {
    const grid = container.querySelector('.booking-dates-grid');
    if (!grid) return;

    grid.innerHTML = '<div style="color:var(--text-3); font-family:monospace;">Loading studio availability calendar...</div>';

    try {
      const res = await fetch(`${API_BASE}/availability`);
      const json = await res.json();
      const days = json.data?.days || [];

      grid.innerHTML = '';
      days.forEach((d) => {
        const tile = document.createElement('button');
        tile.type = 'button';
        tile.className = `date-tile ${state.date === d.date ? 'is-selected' : ''}`;
        tile.innerHTML = `
          <div class="date-tile-day">${d.dayName}</div>
          <div class="date-tile-num">${d.dayNum}</div>
          <div class="date-tile-month">${d.monthName}</div>
        `;
        tile.addEventListener('click', () => {
          grid.querySelectorAll('.date-tile').forEach((t) => t.classList.remove('is-selected'));
          tile.classList.add('is-selected');
          state.date = d.date;
          state.dateDisplay = `${d.dayName}, ${d.dayNum} ${d.monthName}`;
          state.time = '';
          state.timeDisplay = '';
          updateSidebar();
          updateButtons();
        });
        grid.appendChild(tile);
      });
    } catch (err) {
      grid.innerHTML = '<div style="color:var(--red);">Failed to load calendar. Please refresh.</div>';
    }
  }

  // -------------------------------------------------------------
  // STEP 5: TIME SLOTS
  // -------------------------------------------------------------
  async function loadTimeSlots() {
    const grid = container.querySelector('.booking-slots-grid');
    if (!grid) return;

    if (!state.date) {
      grid.innerHTML = '<div style="color:var(--text-3);">Please select a date in Step 4 first.</div>';
      return;
    }

    grid.innerHTML = '<div style="color:var(--text-3); font-family:monospace;">Fetching slot capacity...</div>';

    try {
      const res = await fetch(`${API_BASE}/availability?date=${state.date}&service=${state.serviceSlug}`);
      const json = await res.json();
      const slots = json.data?.slots || [];

      grid.innerHTML = '';
      slots.forEach((s) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `slot-btn ${state.time === s.time ? 'is-selected' : ''} ${!s.available ? 'is-disabled' : ''}`;
        btn.disabled = !s.available;
        btn.innerHTML = `
          <span class="slot-time-text">${s.displayTime}</span>
          <span class="slot-status-text">${s.available ? `${s.remaining} bay(s) open` : 'Fully booked'}</span>
        `;
        btn.addEventListener('click', () => {
          grid.querySelectorAll('.slot-btn').forEach((b) => b.classList.remove('is-selected'));
          btn.classList.add('is-selected');
          state.time = s.time;
          state.timeDisplay = s.displayTime;
          updateSidebar();
          updateButtons();
        });
        grid.appendChild(tileOrBtn(btn));
      });
    } catch (err) {
      grid.innerHTML = '<div style="color:var(--red);">Failed to load time slots.</div>';
    }
  }

  function tileOrBtn(el) { return el; }

  // -------------------------------------------------------------
  // STEP 6: CUSTOMER DETAILS
  // -------------------------------------------------------------
  const nameInput = container.querySelector('input[name="customer_name"]');
  const phoneInput = container.querySelector('input[name="customer_phone"]');
  const whatsappInput = container.querySelector('input[name="customer_whatsapp"]');
  const sameWaCheckbox = container.querySelector('input[name="whatsapp_same"]');
  const emailInput = container.querySelector('input[name="customer_email"]');
  const notesInput = container.querySelector('textarea[name="notes"]');

  if (nameInput) {
    nameInput.value = state.name;
    nameInput.addEventListener('input', (e) => {
      state.name = e.target.value.trim();
      updateSidebar();
      updateButtons();
    });
  }

  if (phoneInput) {
    phoneInput.value = state.phone;
    phoneInput.addEventListener('input', (e) => {
      state.phone = e.target.value.replace(/[^0-9]/g, '').slice(0, 10);
      e.target.value = state.phone;
      if (state.whatsappSameAsPhone) {
        state.whatsapp = state.phone;
        if (whatsappInput) whatsappInput.value = state.phone;
      }
      updateSidebar();
      updateButtons();
    });
  }

  if (sameWaCheckbox) {
    sameWaCheckbox.checked = state.whatsappSameAsPhone;
    sameWaCheckbox.addEventListener('change', (e) => {
      state.whatsappSameAsPhone = e.target.checked;
      if (whatsappInput) {
        whatsappInput.disabled = state.whatsappSameAsPhone;
        if (state.whatsappSameAsPhone) {
          state.whatsapp = state.phone;
          whatsappInput.value = state.phone;
        }
      }
      updateButtons();
    });
  }

  if (whatsappInput) {
    whatsappInput.disabled = state.whatsappSameAsPhone;
    whatsappInput.value = state.whatsapp || state.phone;
    whatsappInput.addEventListener('input', (e) => {
      state.whatsapp = e.target.value.replace(/[^0-9]/g, '').slice(0, 10);
      e.target.value = state.whatsapp;
    });
  }

  if (emailInput) {
    emailInput.value = state.email;
    emailInput.addEventListener('input', (e) => {
      state.email = e.target.value.trim();
    });
  }

  if (notesInput) {
    notesInput.value = state.notes;
    notesInput.addEventListener('input', (e) => {
      state.notes = e.target.value.trim();
    });
  }

  // -------------------------------------------------------------
  // STEP 7: CONFIRMATION TABLE & SUBMISSION
  // -------------------------------------------------------------
  const consentCheckbox = container.querySelector('input[name="consent"]');
  if (consentCheckbox) {
    consentCheckbox.checked = state.consent;
    consentCheckbox.addEventListener('change', (e) => {
      state.consent = e.target.checked;
      updateButtons();
    });
  }

  function renderConfirmSummary() {
    const table = container.querySelector('.booking-confirm-table');
    if (!table) return;

    table.innerHTML = `
      <div class="confirm-row"><strong>Service:</strong> <span>${state.serviceName}</span></div>
      <div class="confirm-row"><strong>Vehicle:</strong> <span>${state.brand} ${state.model} (${state.vehicleType.toUpperCase()}) ${state.registration ? `• ${state.registration}` : ''}</span></div>
      <div class="confirm-row"><strong>Schedule:</strong> <span>${state.dateDisplay} at ${state.timeDisplay}</span></div>
      <div class="confirm-row"><strong>Client:</strong> <span>${state.name} (${state.phone})</span></div>
      ${state.email ? `<div class="confirm-row"><strong>Email:</strong> <span>${state.email}</span></div>` : ''}
      ${state.notes ? `<div class="confirm-row"><strong>Notes:</strong> <span>${state.notes}</span></div>` : ''}
    `;
  }

  // Submit Handler
  async function submitBooking() {
    submitBtn.disabled = true;
    submitBtn.textContent = 'SUBMITTING BOOKING...';
    hideError();

    const payload = {
      service_slug: state.serviceSlug,
      date: state.date,
      time: state.time,
      vehicle: {
        type: state.vehicleType,
        brand: state.brand,
        model: state.model,
        year: state.year || undefined,
        registration: state.registration || undefined,
        color: state.color || undefined
      },
      customer: {
        name: state.name,
        phone: state.phone,
        whatsapp: state.whatsapp || state.phone,
        email: state.email || undefined
      },
      is_recurring: state.isRecurring,
      notes: state.notes || undefined,
      source: container.dataset.bookingSource || 'booking_flow',
      consent: state.consent,
      _hp: ''
    };

    try {
      const res = await fetch(`${API_BASE}/appointments`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const json = await res.json();

      if (!res.ok || !json.ok) {
        if (json.error?.code === 'SLOT_UNAVAILABLE') {
          showError('The selected time slot was just taken. Please select another slot.');
          goToStep(5);
        } else {
          showError(json.error?.message || 'Failed to confirm appointment. Please try again.');
        }
        submitBtn.disabled = false;
        submitBtn.textContent = 'CONFIRM BOOKING →';
        return;
      }

      // Success: Render Confirmation Screen
      renderSuccessScreen(json.data);
      sessionStorage.removeItem('tdc_booking_state');
    } catch (err) {
      showError('Network error connecting to booking server. Please check your connection.');
      submitBtn.disabled = false;
      submitBtn.textContent = 'CONFIRM BOOKING →';
    }
  }

  function renderSuccessScreen(bookingData) {
    const successStage = container.querySelector('.booking-success-stage');
    const flowStage = container.querySelector('.booking-flow-stage');

    if (flowStage) flowStage.style.display = 'none';
    if (successStage) {
      successStage.style.display = 'block';
      const refEl = successStage.querySelector('.success-ref-code');
      const detailsEl = successStage.querySelector('.success-details-text');
      const icsBtn = successStage.querySelector('.success-ics-btn');
      const waBtn = successStage.querySelector('.success-wa-btn');

      if (refEl) refEl.textContent = bookingData.ref;
      if (detailsEl) {
        detailsEl.textContent = `Scheduled for ${bookingData.date} at ${bookingData.time.substring(0, 5)} for ${bookingData.service_name}.`;
      }
      if (icsBtn) {
        icsBtn.href = `${API_BASE}/appointments/${bookingData.ref}/ics`;
      }
      if (waBtn) {
        const text = encodeURIComponent(`Hi The Drive Clinic, I just booked appointment ${bookingData.ref} for ${bookingData.service_name}.`);
        waBtn.href = `https://wa.me/919419100000?text=${text}`;
      }
    }
  }

  // Navigation Button Handlers
  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      goToStep(state.step - 1);
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      if (validateStep(state.step)) {
        goToStep(state.step + 1);
      }
    });
  }

  if (submitBtn) {
    submitBtn.addEventListener('click', submitBooking);
  }

  // Initialize Flow at Saved Step
  goToStep(state.step);
}
