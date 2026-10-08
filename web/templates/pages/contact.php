<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Contact & Studio Location Template (`/contact`)
$services = $services ?? [];
?>

<!-- 01. CONTACT HERO -->
<section class="section section-dark" style="padding-top: 180px; padding-bottom: 90px;">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">STUDIO LOCATION & ENQUIRIES</div>
    <h1 class="h1-hero" style="color:var(--white); margin-bottom: 24px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">VISIT</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">THE STUDIO.</span></span>
    </h1>
    <div class="redline" style="max-width: 140px; margin-bottom: 24px;" data-anim="redline"></div>
    <p class="lead-text" style="color:var(--grey); max-width: 680px;" data-anim="fade-up">
      Located in Nanak Nagar, Jammu. Drop by for a vehicle paint thickness inspection, studio consultation, or enquiry.
    </p>
  </div>
</section>

<!-- 02. 5/7 SPLIT: DETAILS & MAP -->
<section class="section section-light">
  <div class="container grid-5-7">
    <div>
      <div class="eyebrow" data-anim="fade-up">CONTACT INFO</div>
      <h2 class="h2-section" style="font-size:clamp(36px, 4vw, 56px);" data-anim="text-reveal">
        <span class="anim-text-line"><span class="anim-text-inner">CONNECT</span></span>
        <span class="anim-text-line"><span class="anim-text-inner text-red">WITH US.</span></span>
      </h2>

      <div style="display:flex; flex-direction:column; gap:28px; margin: 36px 0 44px;" data-anim="fade-up">
        <!-- Address -->
        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.18em; text-transform:uppercase; margin-bottom:6px;">
            STUDIO ADDRESS
          </div>
          <p style="font-size:16px; color:var(--ink); line-height:1.6;">
            <?= htmlspecialchars($settings['address'] ?? 'Nanak Nagar, Jammu, Jammu & Kashmir 180004, India') ?>
          </p>
        </div>

        <!-- Phone -->
        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.18em; text-transform:uppercase; margin-bottom:6px;">
            PHONE NUMBER
          </div>
          <a href="tel:<?= htmlspecialchars($settings['phone'] ?? '+91 94191 00000') ?>" style="font-size:18px; font-weight:700; color:var(--ink); transition:color 0.2s;" onmouseover="this.style.color='var(--red)'" onmouseout="this.style.color='var(--ink)'">
            <?= htmlspecialchars($settings['phone'] ?? '+91 94191 00000') ?>
          </a>
        </div>

        <!-- WhatsApp -->
        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.18em; text-transform:uppercase; margin-bottom:6px;">
            WHATSAPP CONCIERGE
          </div>
          <a href="https://wa.me/<?= htmlspecialchars($settings['whatsapp'] ?? '919419100000') ?>" target="_blank" rel="noopener" class="text-link" style="font-size:16px;">
            Chat with Studio on WhatsApp →
          </a>
        </div>

        <!-- Email -->
        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.18em; text-transform:uppercase; margin-bottom:6px;">
            EMAIL ADDRESS
          </div>
          <a href="mailto:<?= htmlspecialchars($settings['email'] ?? 'info@thedriveclinic.com') ?>" class="text-link" style="font-size:16px;">
            <?= htmlspecialchars($settings['email'] ?? 'info@thedriveclinic.com') ?>
          </a>
        </div>

        <!-- Hours -->
        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.18em; text-transform:uppercase; margin-bottom:6px;">
            STUDIO HOURS
          </div>
          <p style="font-size:15px; color:var(--text-2);">
            <?= htmlspecialchars($settings['hours'] ?? 'Monday – Sunday: 09:00 AM – 08:00 PM (Open 7 Days a Week)') ?>
          </p>
        </div>
      </div>

      <div style="display:flex; gap:16px; flex-wrap:wrap;" data-anim="fade-up">
        <a href="https://maps.google.com/?q=The+Drive+Clinic+Nanak+Nagar+Jammu" target="_blank" rel="noopener" class="btn btn-red">GET DIRECTIONS →</a>
        <a href="/book" class="btn btn-ghost-dark">BOOK A SERVICE →</a>
      </div>
    </div>

    <!-- Lazy Google Map Embed -->
    <div>
      <div style="border:1px solid var(--line); overflow:hidden; min-height:460px; background:var(--white);" data-anim="mask-reveal">
        <iframe 
          src="<?= htmlspecialchars($settings['map_embed_url'] ?? 'https://maps.google.com') ?>" 
          width="100%" 
          height="460" 
          style="border:0; display:block;" 
          allowfullscreen="" 
          loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade"
          title="The Drive Clinic Nanak Nagar Jammu Map">
        </iframe>
      </div>
    </div>
  </div>
</section>

<!-- 03. ENQUIRY FORM SECTION (White) -->
<section class="section section-white">
  <div class="container" style="max-width: 800px;">
    <div class="eyebrow" data-anim="fade-up">SEND AN ENQUIRY</div>
    <h2 class="h2-section" style="margin-bottom: 20px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">HAVE A</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">QUESTION?</span></span>
    </h2>
    <p class="lead-text" style="margin-bottom: 36px;" data-anim="fade-up">
      Fill out the form below and our studio team will reach out with estimates and availability.
    </p>

    <div style="background:var(--off); border:1px solid var(--line); padding:44px;" data-anim="fade-up">
      <!-- Status Message Banners -->
      <div id="contact-success-banner" style="display:none; background:#1b3820; color:#b2f2bb; border:1px solid #2b5c32; padding:18px 24px; margin-bottom:24px; font-size:15px; font-weight:600;">
        ✓ Thank you! Your enquiry has been received. The Drive Clinic team will contact you shortly via WhatsApp.
      </div>
      <div id="contact-error-banner" style="display:none; background:#2a1215; color:#ff8a80; border:1px solid #5c2b2e; padding:18px 24px; margin-bottom:24px; font-size:15px; font-weight:600;"></div>

      <form id="contact-enquiry-form" style="display:flex; flex-direction:column; gap:20px;">
        <!-- Honeypot Field -->
        <input type="text" name="_hp" style="display:none;" tabindex="-1" autocomplete="off">

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
          <div>
            <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">Your Name *</label>
            <input type="text" name="name" required placeholder="e.g. Rahul Sharma" style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--white); font-weight:600;">
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">10-Digit Mobile Phone *</label>
            <input type="tel" name="phone" required pattern="[6-9][0-9]{9}" maxlength="10" placeholder="9419100000" style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--white); font-family:monospace; font-size:15px;">
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
          <div>
            <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">Email Address</label>
            <input type="email" name="email" placeholder="rahul@example.com" style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--white);">
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">Vehicle Type</label>
            <select name="vehicle_type" style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--white); font-weight:600;">
              <option value="car">Car / 4-Wheeler</option>
              <option value="bike">Bike / 2-Wheeler</option>
            </select>
          </div>
        </div>

        <div>
          <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">Service of Interest</label>
          <select name="service_slug" style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--white); font-weight:600;">
            <option value="">General Inquiry / Consultation</option>
            <?php foreach ($services as $svc): ?>
              <option value="<?= htmlspecialchars($svc['slug']) ?>"><?= htmlspecialchars($svc['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">Your Message / Query</label>
          <textarea name="message" rows="4" placeholder="Tell us about your vehicle model, current paint condition, or questions..." style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--white);"></textarea>
        </div>

        <button type="submit" id="contact-submit-btn" class="btn btn-red" style="width:100%; margin-top:8px;">
          SEND ENQUIRY →
        </button>
      </form>
    </div>
  </div>
</section>

<!-- AJAX Submission Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('contact-enquiry-form');
  const submitBtn = document.getElementById('contact-submit-btn');
  const successBanner = document.getElementById('contact-success-banner');
  const errorBanner = document.getElementById('contact-error-banner');

  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      successBanner.style.display = 'none';
      errorBanner.style.display = 'none';

      submitBtn.disabled = true;
      submitBtn.textContent = 'SENDING ENQUIRY...';

      const formData = new FormData(form);
      const payload = {
        source: 'contact',
        name: formData.get('name')?.toString().trim(),
        phone: formData.get('phone')?.toString().trim(),
        email: formData.get('email')?.toString().trim() || undefined,
        vehicle_type: formData.get('vehicle_type') || 'car',
        service_slug: formData.get('service_slug') || undefined,
        message: formData.get('message')?.toString().trim() || undefined,
        _hp: formData.get('_hp')?.toString() || ''
      };

      try {
        const res = await fetch('http://localhost:5000/api/leads', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });

        const json = await res.json();

        if (res.ok && json.ok) {
          successBanner.style.display = 'block';
          form.reset();
          submitBtn.textContent = 'ENQUIRY SENT ✓';
        } else {
          errorBanner.textContent = json.error?.message || 'Failed to submit enquiry. Please check your details.';
          errorBanner.style.display = 'block';
          submitBtn.disabled = false;
          submitBtn.textContent = 'SEND ENQUIRY →';
        }
      } catch (err) {
        errorBanner.textContent = 'Network error connecting to API server. Please try again.';
        errorBanner.style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.textContent = 'SEND ENQUIRY →';
      }
    });
  }
});
</script>
