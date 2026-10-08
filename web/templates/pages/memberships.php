<?php
// THE DRIVE CLINIC — Public VIP Membership Club Page
?>
<div class="membership-page-wrap">
  <!-- 01. MEMBERSHIP HERO -->
  <section class="section" style="padding-top: 175px; padding-bottom: 60px; position:relative; overflow:hidden;">
    <div class="container">
      <div class="eyebrow" data-anim="fade-up">VIP PROGRAM // AUTOMOTIVE STEWARDSHIP</div>
      
      <h1 class="h1-hero" style="color:var(--white); margin-bottom: 24px;" data-anim="text-reveal">
        <span class="anim-text-line"><span class="anim-text-inner">THE CLINIC</span></span>
        <span class="anim-text-line"><span class="anim-text-inner text-red">VIP CLUB PASS.</span></span>
      </h1>

      <div class="redline" style="max-width: 140px; margin-bottom: 24px;" data-anim="redline"></div>

      <p class="lead-text" style="color:#e2e8f0; max-width: 680px; margin-bottom: 28px;" data-anim="fade-up">
        Continuous detailing perfection for your vehicle. Enjoy regular swirl-free decontamination washes, annual ceramic rejuvenations, priority studio bay booking, and exclusive member privileges in Jammu.
      </p>

      <div class="memberships-hero-badges" data-anim="fade-up">
        <span class="m-hero-pill">✓ Priority Weekend Bay Access</span>
        <span class="m-hero-pill">✓ Gtechniq Certified Top-Ups</span>
        <span class="m-hero-pill">✓ Digital Studio Pass & WhatsApp Wallet</span>
      </div>
    </div>
  </section>

  <!-- 02. MEMBERSHIP PRICING TIERS -->
  <section class="section" style="padding-top: 20px; padding-bottom: 90px; position:relative;">
    <div class="container">
      
      <div style="text-align: center; margin-bottom: 50px;">
        <div style="font-size:11.5px; font-weight:800; letter-spacing:0.22em; color:var(--red); text-transform:uppercase; margin-bottom:10px;">SELECT YOUR CARE FREQUENCY</div>
        <h2 style="font-family:var(--font-display); font-size:clamp(34px, 5.5vw, 50px); color:#ffffff; letter-spacing:0.04em; text-transform:uppercase; margin:0;">
          VIP SUBSCRIPTION TIERS
        </h2>
      </div>

      <div class="memberships-grid">
        
        <!-- Tier 1: Signature Wash Club -->
        <div class="membership-card">
          <div class="membership-card-top">
            <span class="membership-card-badge">MONTHLY RECURRING</span>
            <h3 class="membership-plan-name">SIGNATURE WASH CLUB</h3>
            <p class="membership-tagline">For drivers who demand swirl-free cleanliness every week.</p>
          </div>

          <div class="membership-price-wrap">
            <span class="membership-currency">₹</span>
            <span class="membership-amount">2,999</span>
            <span class="membership-cycle">/ MONTH</span>
          </div>

          <div class="membership-divider"></div>

          <ul class="membership-features">
            <li>
              <svg class="m-check" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span><strong>3x Decon Foam Washes</strong> per month</span>
            </li>
            <li>
              <svg class="m-check" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Deep Steam Interior Cabin Purge & AC Sanitization</span>
            </li>
            <li>
              <svg class="m-check" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Wheel Barrel De-ironing & High-Gloss Tyre Glaze</span>
            </li>
            <li>
              <svg class="m-check" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Hydrophobic Quick Detailer Spray Sealant</span>
            </li>
            <li>
              <svg class="m-check" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span><strong>10% Member Discount</strong> on clay bar treatments</span>
            </li>
          </ul>

          <div class="membership-cta-wrap">
            <button type="button" class="btn btn-ghost membership-cta" onclick="openEnrollModal('Signature Wash Club', 'Monthly', 2999)">
              JOIN WASH CLUB →
            </button>
          </div>
        </div>

        <!-- Tier 2: Ceramic Care Pass (Featured Flagship) -->
        <div class="membership-card membership-card-featured">
          <div class="featured-ribbon">★ MOST POPULAR</div>
          
          <div class="membership-card-top">
            <span class="membership-card-badge featured-badge-kicker">ANNUAL WARRANTY PASS</span>
            <h3 class="membership-plan-name featured-title">CERAMIC CARE PASS</h3>
            <p class="membership-tagline">Keeps 9H coating hydrophobic, covered & swirl-free all year.</p>
          </div>

          <div class="membership-price-wrap">
            <span class="membership-currency">₹</span>
            <span class="membership-amount">14,999</span>
            <span class="membership-cycle">/ YEAR</span>
          </div>

          <div class="membership-divider"></div>

          <ul class="membership-features">
            <li>
              <svg class="m-check m-check-gold" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span><strong>2x Annual Ceramic Top-Ups</strong> (Gtechniq CSL)</span>
            </li>
            <li>
              <svg class="m-check m-check-gold" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span><strong>6x Signature Studio Washes</strong> included</span>
            </li>
            <li>
              <svg class="m-check m-check-gold" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Windshield & Glass Rain-Repellent Treatment</span>
            </li>
            <li>
              <svg class="m-check m-check-gold" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span><strong>Priority Bay Booking</strong> (Zero wait times on weekends)</span>
            </li>
            <li>
              <svg class="m-check m-check-gold" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Official Ceramic Warranty Renewal Certificate</span>
            </li>
          </ul>

          <div class="membership-cta-wrap">
            <button type="button" class="btn btn-red membership-cta featured-btn" onclick="openEnrollModal('Ceramic Care Pass', 'Annual', 14999)">
              JOIN CERAMIC CLUB →
            </button>
          </div>
        </div>

        <!-- Tier 3: Elite Concierge Detail Pass -->
        <div class="membership-card">
          <div class="membership-card-top">
            <span class="membership-card-badge">CONCIERGE STEWARDSHIP</span>
            <h3 class="membership-plan-name">ELITE CONCIERGE</h3>
            <p class="membership-tagline">Complete automotive stewardship with unlimited valet care.</p>
          </div>

          <div class="membership-price-wrap">
            <span class="membership-currency">₹</span>
            <span class="membership-amount">29,999</span>
            <span class="membership-cycle">/ YEAR</span>
          </div>

          <div class="membership-divider"></div>

          <ul class="membership-features">
            <li>
              <svg class="m-check" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span><strong>12x Comprehensive Studio Washes</strong></span>
            </li>
            <li>
              <svg class="m-check" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Full Annual Multi-Stage Machine Paint Glaze</span>
            </li>
            <li>
              <svg class="m-check" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span><strong>15% VIP Privilege Discount</strong> on STEK PPF</span>
            </li>
            <li>
              <svg class="m-check" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Complimentary Studio Valet Pickup & Drop in Jammu</span>
            </li>
            <li>
              <svg class="m-check" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Emergency Bird Dropping / Stain Removal Consult</span>
            </li>
          </ul>

          <div class="membership-cta-wrap">
            <button type="button" class="btn btn-ghost membership-cta" onclick="openEnrollModal('Elite Concierge Detail Pass', 'Annual', 29999)">
              APPLY FOR CONCIERGE →
            </button>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- 03. MEMBERSHIP PRIVILEGES FAQ -->
  <section class="section" style="border-top:1px solid rgba(255,255,255,0.1); padding: 80px 0;">
    <div class="container" style="max-width: 880px;">
      <div style="text-align: center; margin-bottom: 44px;">
        <div style="font-size:11.5px; font-weight:800; letter-spacing:0.2em; color:var(--red); text-transform:uppercase; margin-bottom:8px;">FREQUENTLY ASKED QUESTIONS</div>
        <h2 style="font-family: var(--font-display); font-size: 38px; color:#ffffff; letter-spacing:0.04em; text-transform:uppercase; margin:0;">MEMBERSHIP PRIVILEGES</h2>
      </div>

      <div class="faq-list">
        <div class="faq-item" style="background: #111111; border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; padding: 24px; margin-bottom: 16px; box-shadow: 0 8px 25px rgba(0,0,0,0.5);">
          <h4 style="font-size: 17px; font-weight:700; color:#ffffff; margin-bottom:10px;">Can I use my washes across multiple vehicles in my family?</h4>
          <p style="font-size: 14.5px; color:#cccccc; line-height:1.65; margin:0;">Yes. Your digital pass allows you to assign unused signature washes to any registered vehicle in your garage by simply notifying the studio reception during check-in.</p>
        </div>

        <div class="faq-item" style="background: #111111; border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; padding: 24px; margin-bottom: 16px; box-shadow: 0 8px 25px rgba(0,0,0,0.5);">
          <h4 style="font-size: 17px; font-weight:700; color:#ffffff; margin-bottom:10px;">How does the ceramic annual top-up work?</h4>
          <p style="font-size: 14.5px; color:#cccccc; line-height:1.65; margin:0;">Our detailers perform a chemical decontamination to unclog ceramic microscopic pores, followed by an application of Gtechniq EXO / CSL booster to restore ultra-gloss and 110° water beading.</p>
        </div>

        <div class="faq-item" style="background: #111111; border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; padding: 24px; box-shadow: 0 8px 25px rgba(0,0,0,0.5);">
          <h4 style="font-size: 17px; font-weight:700; color:#ffffff; margin-bottom:10px;">How do I track my remaining washes and validity?</h4>
          <p style="font-size: 14.5px; color:#cccccc; line-height:1.65; margin:0;">Each member receives an official digital pass code (e.g. <code>TDC-VIP-XXXX</code>). Every studio visit is tracked live in our CRM with instant WhatsApp receipts showing your remaining credits.</p>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Membership Enrollment Modal -->
<div class="crm-modal-backdrop" id="modalPublicEnroll" onclick="handleBackdropClick(event)">
  <div class="crm-modal" onclick="event.stopPropagation()">
    <div class="crm-modal-header">
      <div>
        <h3 class="crm-modal-title">JOIN THE DRIVE CLINIC CLUB</h3>
        <p class="crm-modal-sub" id="enrollModalSubtitle">Activate your recurring detailing pass in Jammu.</p>
      </div>
      <button type="button" class="crm-modal-close" aria-label="Close" onclick="closeModal('modalPublicEnroll')">&times;</button>
    </div>
    <form id="formPublicEnroll" onsubmit="handlePublicEnroll(event)">
      <input type="hidden" name="plan_name" id="enrollPlanName">
      <input type="hidden" name="billing_cycle" id="enrollBillingCycle">
      <input type="hidden" name="price" id="enrollPrice">
      
      <div class="crm-modal-body">
        <div class="enroll-plan-banner">
          <div>
            <span class="enroll-plan-kicker">SELECTED CLUB PASS</span>
            <div id="enrollDisplayPlan" class="enroll-plan-name-display">Signature Wash Club</div>
          </div>
          <div id="enrollDisplayPrice" class="enroll-plan-price-display">₹2,999</div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Full Name *</label>
            <input type="text" name="customer_name" class="crm-input" placeholder="e.g. Vikram Sharma" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Phone / WhatsApp *</label>
            <input type="tel" name="phone" class="crm-input" placeholder="e.g. +91 94191 22334" required>
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Vehicle Brand & Model *</label>
            <input type="text" name="vehicle_model" class="crm-input" placeholder="e.g. BMW M340i / Audi A6" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Registration Plate</label>
            <input type="text" name="vehicle_reg" class="crm-input" placeholder="e.g. JK 02 AB 1234">
          </div>
        </div>

        <div class="crm-form-group">
          <label class="crm-label">Email Address (For Invoices & Passes)</label>
          <input type="email" name="email" class="crm-input" placeholder="e.g. name@example.com">
        </div>
      </div>
      <div class="crm-modal-footer">
        <button type="button" class="btn btn-outline" style="padding:10px 20px; font-size:13px;" onclick="closeModal('modalPublicEnroll')">Cancel</button>
        <button type="submit" class="btn btn-red" style="padding:10px 24px; font-size:13px; font-weight:800;">Activate Pass &rarr;</button>
      </div>
    </form>
  </div>
</div>

<script>
window.openEnrollModal = function(planName, cycle, price) {
  const planInput = document.getElementById('enrollPlanName');
  const cycleInput = document.getElementById('enrollBillingCycle');
  const priceInput = document.getElementById('enrollPrice');
  const displayPlan = document.getElementById('enrollDisplayPlan');
  const displayPrice = document.getElementById('enrollDisplayPrice');
  const modal = document.getElementById('modalPublicEnroll');

  if (planInput) planInput.value = planName;
  if (cycleInput) cycleInput.value = cycle;
  if (priceInput) priceInput.value = price;
  if (displayPlan) displayPlan.textContent = planName + ' (' + cycle + ')';
  if (displayPrice) displayPrice.textContent = '₹' + Number(price).toLocaleString('en-IN');
  if (modal) modal.classList.add('active');
};

window.closeModal = function(modalId) {
  const target = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
  if (target) {
    target.classList.remove('active');
  }
};

window.handleBackdropClick = function(e) {
  if (e.target.classList.contains('crm-modal-backdrop')) {
    e.target.classList.remove('active');
  }
};

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    const activeModal = document.querySelector('.crm-modal-backdrop.active');
    if (activeModal) {
      activeModal.classList.remove('active');
    }
  }
});

async function handlePublicEnroll(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Activating Pass...';

  try {
    const res = await fetch('/crm/api/memberships/create', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    if (data.success) {
      alert('🎉 Welcome to The Drive Clinic Club!\n\nYour membership pass has been generated: ' + (data.member_no || 'TDC-VIP-MEMBER') + '.\nWe look forward to seeing your vehicle at our studio in Jammu.');
      closeModal('modalPublicEnroll');
      form.reset();
    } else {
      alert('Error: ' + (data.error || 'Could not complete enrollment'));
    }
  } catch (err) {
    alert('Network error enrolling into membership. Please try again or WhatsApp us directly.');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Activate Pass →';
  }
}
</script>
