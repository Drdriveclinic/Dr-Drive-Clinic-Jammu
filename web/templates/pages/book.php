<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Split-Screen 7-Step Online Booking Engine
$services = $services ?? [];
$preselectedService = $preselectedService ?? '';
$bookingSource = $bookingSource ?? 'booking_flow';
$isRecurring = isset($_GET['recurring']) && $_GET['recurring'] === '1';
?>

<div class="booking-flow-container" 
     data-preselected-service="<?= htmlspecialchars($preselectedService) ?>" 
     data-booking-source="<?= htmlspecialchars($bookingSource) ?>"
     data-recurring="<?= $isRecurring ? '1' : '0' ?>"
     style="min-height: 100vh; background-color: var(--off); padding-top: 84px;">

  <!-- Main Flow Stage -->
  <div class="booking-flow-stage" style="display: flex; min-height: calc(100vh - 84px);">
    
    <!-- LEFT 5/12: Dark Live Summary Sidebar (Desktop) -->
    <aside class="booking-sidebar" style="flex: 0 0 41.666%; background-color: var(--black); color: var(--white); padding: 60px 48px; display: flex; flex-direction: column; justify-content: space-between; border-right: 1px solid rgba(255,255,255,0.1);">
      <div>
        <div class="eyebrow" style="color:var(--white);">ONLINE SCHEDULING</div>
        <h1 class="h1-hero" style="font-size:clamp(48px, 4.5vw, 76px); margin-bottom: 24px;">
          BOOK YOUR<br><span class="text-red">SERVICE.</span>
        </h1>
        <div class="redline" style="max-width: 100px; margin-bottom: 36px;"></div>

        <!-- Live Visual Placeholder Tile -->
        <div class="img-placeholder" style="min-height: 220px; margin-bottom: 36px; border: 1px solid rgba(255,255,255,0.12);">
          <span class="booking-sidebar-photo-caption">PHOTO — Premium Automotive Clean Bay & PPF Studio</span>
        </div>

        <!-- Live Summary List -->
        <div class="booking-summary-wrapper">
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.18em; text-transform:uppercase; margin-bottom:16px;">
            BOOKING OVERVIEW
          </div>
          <div class="booking-summary-list" style="display:flex; flex-direction:column; gap:12px; font-size:14px;">
            <!-- Rendered dynamically by booking-flow.js -->
          </div>
        </div>
      </div>

      <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 24px; font-size: 13px; color: var(--text-3);">
        Need immediate assistance? <a href="tel:<?= htmlspecialchars($settings['phone'] ?? '+91 94191 00000') ?>" style="color:var(--white); font-weight:700;">Call Studio</a> or <a href="https://wa.me/<?= htmlspecialchars($settings['whatsapp'] ?? '919419100000') ?>" target="_blank" rel="noopener" style="color:var(--red);">WhatsApp</a>
      </div>
    </aside>

    <!-- RIGHT 7/12: Step Area -->
    <main class="booking-step-area" style="flex: 1; padding: 60px 48px; background-color: var(--white); display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <!-- Top Progress Bar (7 Segments) -->
        <div class="booking-top-bar" style="margin-bottom: 40px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <span class="booking-step-counter" style="font-size:12px; font-weight:800; letter-spacing:0.15em; color:var(--red);">
              STEP 01 / 07
            </span>
            <span class="booking-step-name" style="font-size:12px; font-weight:700; letter-spacing:0.15em; color:var(--ink); text-transform:uppercase;">
              · VEHICLE TYPE
            </span>
          </div>

          <div class="booking-progress-track" style="display:flex; gap:6px; height:4px; background:transparent;">
            <div class="booking-progress-segment is-active" style="flex:1; background:var(--line); transition:background 0.2s;"></div>
            <div class="booking-progress-segment" style="flex:1; background:var(--line); transition:background 0.2s;"></div>
            <div class="booking-progress-segment" style="flex:1; background:var(--line); transition:background 0.2s;"></div>
            <div class="booking-progress-segment" style="flex:1; background:var(--line); transition:background 0.2s;"></div>
            <div class="booking-progress-segment" style="flex:1; background:var(--line); transition:background 0.2s;"></div>
            <div class="booking-progress-segment" style="flex:1; background:var(--line); transition:background 0.2s;"></div>
            <div class="booking-progress-segment" style="flex:1; background:var(--line); transition:background 0.2s;"></div>
          </div>
        </div>

        <!-- Error Banner -->
        <div class="booking-error-banner" style="display:none; background:#2a1215; color:#ff8a80; border:1px solid #5c2b2e; padding:16px 20px; font-size:14px; margin-bottom:24px; font-weight:600;"></div>

        <!-- ================================================================
             STEP 1: VEHICLE TYPE
             ================================================================ -->
        <div class="booking-step-panel" data-step="1">
          <h2 class="h2-section" style="font-size:36px; margin-bottom:12px;">SELECT VEHICLE TYPE</h2>
          <p style="color:var(--text-2); margin-bottom:32px;">Choose whether you are bringing in a four-wheeler or a two-wheeler.</p>
          
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; max-width:600px;">
            <button type="button" class="type-tile is-selected" data-type="car" style="padding:40px 24px; border:2px solid var(--black); background:var(--off); text-align:center; cursor:pointer; transition:all 0.2s ease;">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="margin:0 auto 16px; display:block;"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.5 3c-.1.2-.1.4-.1.7V16c0 .6.4 1 1 1h2m10 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-10 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
              <span style="font-family:var(--font-display); font-size:28px; text-transform:uppercase; display:block;">CAR / 4-WHEELER</span>
            </button>

            <button type="button" class="type-tile" data-type="bike" style="padding:40px 24px; border:1px solid var(--line); background:var(--off); text-align:center; cursor:pointer; transition:all 0.2s ease;">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="margin:0 auto 16px; display:block;"><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/><path d="M15 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-3 11.5L9 9l3-3 2 3h4"/></svg>
              <span style="font-family:var(--font-display); font-size:28px; text-transform:uppercase; display:block;">BIKE / 2-WHEELER</span>
            </button>
          </div>
        </div>

        <!-- ================================================================
             STEP 2: SERVICE SELECTION
             ================================================================ -->
        <div class="booking-step-panel" data-step="2" style="display:none;">
          <h2 class="h2-section" style="font-size:36px; margin-bottom:12px;">CHOOSE SERVICE</h2>
          <p style="color:var(--text-2); margin-bottom:28px;">Select from our detailing packages, PPF films, or studio wash treatments.</p>

          <div class="booking-services-list" style="display:flex; flex-direction:column; gap:16px; max-height:480px; overflow-y:auto; padding-right:12px;">
            <?php foreach ($services as $svc): ?>
              <div class="booking-service-card <?= $svc['slug'] === $preselectedService ? 'is-selected' : '' ?>"
                   data-slug="<?= htmlspecialchars($svc['slug']) ?>"
                   data-name="<?= htmlspecialchars($svc['name']) ?>"
                   data-category="<?= htmlspecialchars($svc['category']) ?>"
                   data-vehicle-types="<?= htmlspecialchars($svc['vehicle_types']) ?>"
                   data-duration="<?= htmlspecialchars($svc['duration_note'] ?: '[CONFIRM DURATION]') ?>"
                   style="border:1px solid var(--line); padding:24px; background:var(--off); cursor:pointer; transition:all 0.2s ease;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                  <div>
                    <span style="font-size:11px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase;">
                      <?= htmlspecialchars(strtoupper($svc['category'])) ?>
                    </span>
                    <h4 style="font-family:var(--font-display); font-size:26px; text-transform:uppercase; margin-top:2px;">
                      <?= htmlspecialchars($svc['name']) ?>
                    </h4>
                  </div>
                  <span style="font-size:12px; font-weight:700; color:var(--text-3); font-family:monospace;">
                    <?= htmlspecialchars($svc['duration_note'] ?: 'Variable') ?>
                  </span>
                </div>
                <p style="font-size:14px; color:var(--text-2); line-height:1.5;">
                  <?= htmlspecialchars($svc['short_desc']) ?>
                </p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- ================================================================
             STEP 3: VEHICLE DETAILS
             ================================================================ -->
        <div class="booking-step-panel" data-step="3" style="display:none;">
          <h2 class="h2-section" style="font-size:36px; margin-bottom:12px;">VEHICLE SPECIFICATIONS</h2>
          <p style="color:var(--text-2); margin-bottom:28px;">Help us prepare the correct detailing chemicals and bay dimensions for your ride.</p>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; max-width:680px;">
            <div>
              <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">Brand / Make *</label>
              <input type="text" name="brand" placeholder="e.g. BMW, Mercedes, Royal Enfield" required style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--off); font-weight:600;">
            </div>

            <div>
              <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">Model *</label>
              <input type="text" name="model" placeholder="e.g. 3 Series, Continental GT" required style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--off); font-weight:600;">
            </div>

            <div>
              <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">Manufacturing Year</label>
              <input type="text" name="year" placeholder="e.g. 2024" maxlength="4" style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--off);">
            </div>

            <div>
              <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">Registration Number</label>
              <input type="text" name="registration" placeholder="e.g. JK 02 AB 1234" maxlength="20" style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--off); text-transform:uppercase; font-family:monospace;">
            </div>

            <div style="grid-column: span 2;">
              <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">Vehicle Color</label>
              <input type="text" name="color" placeholder="e.g. Tanzanite Blue Metallic" style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--off);">
            </div>
          </div>
        </div>

        <!-- ================================================================
             STEP 4: SELECT DATE
             ================================================================ -->
        <div class="booking-step-panel" data-step="4" style="display:none;">
          <h2 class="h2-section" style="font-size:36px; margin-bottom:12px;">SELECT APPOINTMENT DATE</h2>
          <p style="color:var(--text-2); margin-bottom:28px;">Pick a date within the next 14 days. Studio opens 7 days a week.</p>

          <div class="booking-dates-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(110px, 1fr)); gap:12px;">
            <!-- Loaded dynamically from /api/availability -->
          </div>
        </div>

        <!-- ================================================================
             STEP 5: SELECT TIME SLOT
             ================================================================ -->
        <div class="booking-step-panel" data-step="5" style="display:none;">
          <h2 class="h2-section" style="font-size:36px; margin-bottom:12px;">SELECT TIME SLOT</h2>
          <p style="color:var(--text-2); margin-bottom:28px;">Bay capacity is monitored in real-time. Fully booked slots are disabled.</p>

          <div class="booking-slots-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(160px, 1fr)); gap:14px;">
            <!-- Loaded dynamically from /api/availability?date= -->
          </div>
        </div>

        <!-- ================================================================
             STEP 6: CONTACT DETAILS
             ================================================================ -->
        <div class="booking-step-panel" data-step="6" style="display:none;">
          <h2 class="h2-section" style="font-size:36px; margin-bottom:12px;">YOUR CONTACT INFO</h2>
          <p style="color:var(--text-2); margin-bottom:28px;">We will confirm your appointment and slot lock via WhatsApp.</p>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; max-width:680px;">
            <div style="grid-column: span 2;">
              <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">Full Name *</label>
              <input type="text" name="customer_name" required placeholder="e.g. Rahul Sharma" style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--off); font-weight:600;">
            </div>

            <div>
              <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">Phone (10-Digit Mobile) *</label>
              <input type="tel" name="customer_phone" required pattern="[6-9][0-9]{9}" placeholder="9419100000" maxlength="10" style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--off); font-family:monospace; font-size:16px;">
            </div>

            <div>
              <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">WhatsApp Number</label>
              <input type="tel" name="customer_whatsapp" disabled placeholder="Same as phone" maxlength="10" style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--off); font-family:monospace; font-size:16px;">
              <label style="display:flex; align-items:center; gap:8px; font-size:12px; margin-top:6px; cursor:pointer;">
                <input type="checkbox" name="whatsapp_same" checked style="accent-color:var(--red);">
                Same as mobile phone
              </label>
            </div>

            <div style="grid-column: span 2;">
              <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">Email Address (Optional)</label>
              <input type="email" name="customer_email" placeholder="rahul@example.com" style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--off);">
            </div>

            <div style="grid-column: span 2;">
              <label style="display:block; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:6px;">Special Requests / Notes</label>
              <textarea name="notes" rows="3" placeholder="Tell us if your vehicle has pre-existing scratches, ceramic coatings, or specific focus areas..." style="width:100%; border:1px solid var(--line); padding:14px 16px; background:var(--off);"></textarea>
            </div>
          </div>
        </div>

        <!-- ================================================================
             STEP 7: CONFIRMATION
             ================================================================ -->
        <div class="booking-step-panel" data-step="7" style="display:none;">
          <h2 class="h2-section" style="font-size:36px; margin-bottom:12px;">REVIEW & CONFIRM</h2>
          <p style="color:var(--text-2); margin-bottom:28px;">Please review your appointment summary before final confirmation.</p>

          <div style="background:var(--off); border:1px solid var(--line); padding:32px; max-width:680px; margin-bottom:28px;">
            <div class="booking-confirm-table" style="display:flex; flex-direction:column; gap:14px; font-size:15px;">
              <!-- Rendered dynamically -->
            </div>
          </div>

          <label style="display:flex; align-items:flex-start; gap:12px; font-size:13px; color:var(--ink); line-height:1.6; max-width:680px; cursor:pointer;">
            <input type="checkbox" name="consent" style="accent-color:var(--red); margin-top:4px; width:18px; height:18px;">
            <span>I agree to be contacted on WhatsApp / phone regarding this booking confirmation and understand that bay allocations are subject to studio arrival inspection.</span>
          </label>
        </div>
      </div>

      <!-- Bottom Step Navigation Buttons (Sticky on Mobile) -->
      <div class="booking-actions-bar" style="border-top:1px solid var(--line); padding-top:24px; margin-top:40px; display:flex; justify-content:space-between; align-items:center;">
        <button type="button" class="btn btn-ghost-dark booking-prev-btn" style="visibility:hidden;">
          ← BACK
        </button>

        <button type="button" class="btn btn-red booking-next-btn">
          CONTINUE →
        </button>

        <button type="button" class="btn btn-red booking-submit-btn" style="display:none;">
          CONFIRM BOOKING →
        </button>
      </div>
    </main>
  </div>

  <!-- Confirmation Success Stage (Hidden until submit) -->
  <div class="booking-success-stage" style="display:none; padding:100px var(--container-padding); text-align:center; max-width:760px; margin:0 auto;">
    <div style="width:72px; height:72px; background:var(--red); color:var(--white); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 28px; font-size:32px; font-weight:800;">
      ✓
    </div>

    <div class="eyebrow" style="justify-content:center;">RESERVATION RECEIVED</div>
    <h2 class="h1-hero" style="font-size:clamp(48px, 6vw, 84px); margin-bottom:20px;">
      APPOINTMENT<br><span class="text-red">CONFIRMED.</span>
    </h2>

    <div style="background:var(--black); color:var(--white); padding:24px 32px; display:inline-block; margin-bottom:28px;">
      <div style="font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:var(--text-3); margin-bottom:6px;">APPOINTMENT REFERENCE</div>
      <div class="success-ref-code" style="font-family:var(--font-display); font-size:42px; color:var(--red); letter-spacing:0.06em;">
        DRC-XXXXX
      </div>
    </div>

    <p class="success-details-text lead-text" style="color:var(--text-2); margin-bottom:12px;">
      Scheduled for your selected date and time.
    </p>

    <p style="color:var(--text-3); font-size:14px; line-height:1.6; margin-bottom:40px;">
      The team will confirm on WhatsApp shortly with driving directions and bay preparation details.
    </p>

    <div style="display:flex; justify-content:center; gap:16px; flex-wrap:wrap;">
      <a href="/" class="btn btn-dark">BACK TO HOME →</a>
      <a href="#" class="btn btn-red success-ics-btn" download>ADD TO CALENDAR (.ICS) →</a>
      <a href="https://wa.me/919419100000" target="_blank" rel="noopener" class="btn btn-ghost-dark success-wa-btn">WHATSAPP US →</a>
    </div>
  </div>
</div>

<script>
window.THE_DRIVE_CLINIC_CONFIG = {
  apiBaseUrl: 'http://localhost:5000/api'
};
</script>
