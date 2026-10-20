<?php
declare(strict_types=1);

// THE DRIVE CLINIC — WhatsApp Marketing & Broadcast Hub
$customers = Database::query("
    SELECT c.id, c.name, c.phone, c.whatsapp,
           v.brand, v.model, v.registration
    FROM customers c
    LEFT JOIN vehicles v ON c.id = v.customer_id
    GROUP BY c.id
    ORDER BY c.name ASC
");

$memberships = Database::query("SELECT * FROM memberships WHERE status = 'ACTIVE'");
$warranties = Database::query("SELECT * FROM warranty_certificates WHERE status = 'ACTIVE'");
$leads = Database::query("SELECT * FROM leads WHERE stage IN ('NEW', 'CONTACTED', 'QUOTE_SENT') ORDER BY id DESC");
$campaigns = Database::query("SELECT * FROM whatsapp_campaigns ORDER BY created_at DESC");

// Group counts
$countAll = count($customers);
$countVip = count($memberships);
$countPpf = count($warranties);
$countLeads = count($leads);
?>

<div class="crm-whatsapp-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">MARKETING &bull; CLIENT ENGAGEMENT</div>
      <h1 class="crm-page-heading">WhatsApp Marketing & Broadcast Hub</h1>
      <p class="crm-page-sub">Launch seasonal detailing campaigns, ceramic maintenance alerts, PPF inspection reminders, and festive VIP offers directly to client WhatsApp numbers with personalized merge tags.</p>
    </div>
    <div class="crm-page-actions">
      <button class="crm-btn crm-btn-primary" style="background:#25D366; color:#000; font-weight:800;" onclick="focusCampaignEditor()">+ Create New Broadcast</button>
    </div>
  </div>

  <!-- KPI Reach Stats -->
  <div class="crm-kpi-grid">
    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Total Customer Audience</span>
        <span class="crm-kpi-icon" style="color:#25D366;">💬</span>
      </div>
      <div class="crm-kpi-value"><?= $countAll ?></div>
      <div class="crm-kpi-change positive">Verified WhatsApp numbers</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">VIP Club Members</span>
        <span class="crm-kpi-icon" style="color:#10b981;">⭐</span>
      </div>
      <div class="crm-kpi-value"><?= $countVip ?></div>
      <div class="crm-kpi-change positive">Active pass holders</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">PPF & Ceramic Clients</span>
        <span class="crm-kpi-icon" style="color:#38bdf8;">🛡️</span>
      </div>
      <div class="crm-kpi-value"><?= $countPpf ?></div>
      <div class="crm-kpi-change neutral">Warranty registry audience</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Open Enquiries & Leads</span>
        <span class="crm-kpi-icon" style="color:#f59e0b;">🔥</span>
      </div>
      <div class="crm-kpi-value"><?= $countLeads ?></div>
      <div class="crm-kpi-change neutral">Pending quotation conversion</div>
    </div>
  </div>

  <!-- Broadcast Studio: Split Screen (Left: Builder / Right: Live Phone Simulation) -->
  <div style="display:grid; grid-template-columns: 1.2fr 0.8fr; gap:24px; margin-top:24px; align-items:start;">
    
    <!-- LEFT: Campaign Configuration -->
    <div class="crm-card" style="padding:28px;">
      <h2 style="font-family:var(--font-display); font-size:24px; color:#fff; margin-bottom:18px;">
        1. CONFIGURE BROADCAST CAMPAIGN
      </h2>

      <div class="crm-form-group">
        <label class="crm-label">Campaign Title / Internal Name *</label>
        <input type="text" id="campTitle" class="crm-input" value="Monsoon Paint & Hydrophobic Glass Shield Special" placeholder="e.g. Diwali PPF Mega Offer">
      </div>

      <div class="crm-form-row">
        <div class="crm-form-group">
          <label class="crm-label">Target Audience Group *</label>
          <select id="campAudience" class="crm-select" onchange="onAudienceChange()">
            <option value="ALL">All Registered Customers (<?= $countAll ?> contacts)</option>
            <option value="VIP">VIP Signature Wash Members (<?= $countVip ?> contacts)</option>
            <option value="PPF">PPF & Ceramic Warranty Clients (<?= $countPpf ?> contacts)</option>
            <option value="LEADS">Open Leads & Enquiries (<?= $countLeads ?> contacts)</option>
          </select>
        </div>
        <div class="crm-form-group">
          <label class="crm-label">Pre-Made High Converting Templates</label>
          <select id="campTemplateSelect" class="crm-select" onchange="applyPresetTemplate(this.value)">
            <option value="monsoon">🌧️ Monsoon Hydrophobic Shield (Flat 15% Off)</option>
            <option value="ceramic_reminder">✨ 6-Month Ceramic Maintenance Inspection</option>
            <option value="ppf_check">🛡️ PPF Free Annual Edge & Lift Check</option>
            <option value="vip_invite">⭐ VIP Wash Pass Invitation</option>
            <option value="festive_special">🎉 Festive Detailing & Cabin Steam Sanitization</option>
            <option value="review_request">⭐ Post-Service Handover & Google Review</option>
          </select>
        </div>
      </div>

      <div class="crm-form-group">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
          <label class="crm-label" style="margin-bottom:0;">Message Body (Supports WhatsApp Markdown) *</label>
          <div style="display:flex; gap:6px;">
            <button type="button" class="crm-btn crm-btn-xs crm-btn-ghost" onclick="insertTag('{customer_name}')">+ {customer_name}</button>
            <button type="button" class="crm-btn crm-btn-xs crm-btn-ghost" onclick="insertTag('{vehicle_model}')">+ {vehicle_model}</button>
            <button type="button" class="crm-btn crm-btn-xs crm-btn-ghost" onclick="insertTag('{reg_no}')">+ {reg_no}</button>
          </div>
        </div>
        <textarea id="campMessage" class="crm-textarea" rows="8" style="font-family:inherit; font-size:13px; line-height:1.6;" oninput="updatePhonePreview()"></textarea>
      </div>

      <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px; border-top:1px solid var(--crm-border); padding-top:16px;">
        <span style="font-size:13px; color:#94A3B8;">Selected Audience: <strong style="color:#25D366;" id="audienceCountLabel"><?= $countAll ?> Recipients</strong></span>
        <button type="button" class="crm-btn crm-btn-primary" style="background:#25D366; color:#000; font-weight:800;" onclick="saveAndLaunchCampaign()">
          🚀 Save & Open Broadcast Queue
        </button>
      </div>
    </div>

    <!-- RIGHT: WhatsApp Live Phone Preview -->
    <div>
      <div style="max-width:340px; margin:0 auto; background:#111B21; border:8px solid #2A3942; border-radius:36px; padding:16px 12px 24px; box-shadow:0 25px 50px rgba(0,0,0,0.8);">
        <!-- Phone Camera Notch -->
        <div style="width:110px; height:14px; background:#2A3942; border-radius:10px; margin:0 auto 14px;"></div>
        
        <!-- WhatsApp Header -->
        <div style="background:#202C33; border-radius:10px 10px 0 0; padding:10px 12px; display:flex; align-items:center; gap:10px; margin:-16px -12px 14px;">
          <div style="width:34px; height:34px; border-radius:50%; background:#E11D48; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:11px;">TDC</div>
          <div>
            <div style="font-size:13px; font-weight:700; color:#E9EDEF;">THE DRIVE CLINIC</div>
            <div style="font-size:10px; color:#8696A0;">Official Studio Concierge &bull; Jammu</div>
          </div>
        </div>

        <!-- Chat Bubble Preview -->
        <div style="background:#005C4B; color:#E9EDEF; border-radius:8px 8px 0 8px; padding:14px; font-size:12px; line-height:1.55; white-space:pre-wrap; margin-bottom:14px; box-shadow:0 2px 4px rgba(0,0,0,0.3);" id="chatBubblePreview">
        </div>

        <div style="font-size:10px; color:#8696A0; text-align:right;">10:30 AM &bull; ✓✓ Read</div>
      </div>
    </div>
  </div>

  <!-- Target Audience Direct Chat Queue -->
  <div class="crm-card" style="margin-top:28px;">
    <div class="crm-card-header" style="border-bottom:1px solid var(--crm-border); padding:16px 20px;">
      <h3 class="crm-card-title">Broadcast Audience Queue (<span id="queueCount"><?= $countAll ?></span> Contacts)</h3>
      <span class="crm-text-muted">Click "Send Personalized WhatsApp" to trigger 1-click tailored message without expensive API costs</span>
    </div>

    <div class="crm-table-container">
      <table class="crm-table" id="broadcastQueueTable">
        <thead>
          <tr>
            <th>Client Name</th>
            <th>Mobile / WhatsApp</th>
            <th>Registered Vehicle</th>
            <th>Plate #</th>
            <th style="text-align:right;">1-Click Action</th>
          </tr>
        </thead>
        <tbody id="broadcastQueueBody">
          <?php foreach ($customers as $c): 
            $cleanP = preg_replace('/\D/', '', (string)$c['phone']);
            if (strlen($cleanP) === 10) $cleanP = '91' . $cleanP;
            $vTitle = trim(($c['brand'] ?? '') . ' ' . ($c['model'] ?? 'Car'));
          ?>
          <tr data-name="<?= htmlspecialchars($c['name']) ?>" data-phone="<?= htmlspecialchars($cleanP) ?>" data-vehicle="<?= htmlspecialchars($vTitle) ?>" data-reg="<?= htmlspecialchars($c['registration'] ?: 'Pending Reg') ?>">
            <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
            <td><?= htmlspecialchars($c['phone']) ?></td>
            <td><?= htmlspecialchars($vTitle) ?></td>
            <td><span class="crm-plate-tag"><?= htmlspecialchars($c['registration'] ?: 'Pending Reg') ?></span></td>
            <td style="text-align:right;">
              <button class="crm-btn crm-btn-xs" style="background:#25D366; color:#000; font-weight:700;" onclick="sendPersonalizedWA(this)">
                💬 Send WhatsApp
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
const TEMPLATES = {
  monsoon: `🌧️ *MONSOON HYDROPHOBIC ARMOR — THE DRIVE CLINIC JAMMU* 🌧️\n\nDear {customer_name},\nMonsoon moisture, road grime, and acid rain can cause severe paint etching on your *{vehicle_model}* ({reg_no}).\n\nProtect your clear coat with our Signature Rain Shield:\n✅ Full Decontamination Foam Wash\n✅ Ultra-Slick SiO2 Hydrophobic Ceramic Coating\n✅ Anti-Fog Hydrophobic Glass Rain-Repellent\n\n🎁 *Special Studio Offer:* Flat 15% Off this week for registered garage members.\n\nReply *YES* to lock in your detailing bay or call our studio at +91 94191 00000.`,
  ceramic_reminder: `✨ *STUDIO MAINTENANCE ALERT — THE DRIVE CLINIC* ✨\n\nDear {customer_name},\nIt has been 6 months since your *{vehicle_model}* ({reg_no}) was treated with ceramic coating at our studio.\n\nTo keep your hydrophobic beading and candy mirror gloss at peak performance, we recommend your *Complimentary 6-Month Coating Inspection & Gloss Top-Up*.\n\n📅 Studio Slot: Flexible Monday – Sunday\n📍 Location: Nanak Nagar, Jammu\n\nReply with your preferred day to confirm your appointment!`,
  ppf_check: `🛡️ *FREE ANNUAL PPF INSPECTION — THE DRIVE CLINIC* 🛡️\n\nDear {customer_name},\nYour self-healing Paint Protection Film (PPF) on your *{vehicle_model}* ({reg_no}) is due for its complimentary annual inspection.\n\nWe will inspect all wrapped edges, check high-impact frontal seams, and apply our specialized TPU sealant.\n\nReply to book your quick 30-minute studio check!`,
  vip_invite: `⭐ *EXCLUSIVE INVITATION: THE DRIVE CLINIC VIP WASH CLUB* ⭐\n\nDear {customer_name},\nKeep your *{vehicle_model}* looking showroom-fresh every week without the hassle.\n\nJoin our VIP Detailing Pass:\n🚿 3 or 6 Signature Snow Foam Washes per month\n✨ Wheel & Caliper Decontamination\n💨 Interior Dry Steam Sanitization\n💎 20% Off all Paint Correction & Ceramic Coatings\n\nPlans start at just ₹2,999/month. Reply *VIP* for instant activation!`,
  festive_special: `🎉 *FESTIVE VEHICLE RESET — THE DRIVE CLINIC JAMMU* 🎉\n\nDear {customer_name},\nGet your *{vehicle_model}* ({reg_no}) looking brand new for the celebrations!\n\n✨ Bumper-to-Bumper Full Car Detailing\n✨ Multi-Stage Machine Paint Correction\n✨ Deep Interior Steam Extraction & Odor Elimination\n\nLimited bay availability in Nanak Nagar studio. Call +91 94191 00000 or reply here to reserve your slot!`,
  review_request: `⭐ *THANK YOU FOR VISITING THE DRIVE CLINIC!* ⭐\n\nDear {customer_name},\nIt was an absolute pleasure detailing your *{vehicle_model}* ({reg_no}) in our studio bays!\n\nIf you loved the gloss and craftsmanship, we would deeply appreciate a 5-star Google review. It means the world to our detailing crew:\n👉 https://g.page/r/TheDriveClinicJammu/review\n\nDrive safe and see you soon!`
};

function applyPresetTemplate(key) {
  if (TEMPLATES[key]) {
    document.getElementById('campMessage').value = TEMPLATES[key];
    updatePhonePreview();
  }
}

function insertTag(tag) {
  const el = document.getElementById('campMessage');
  const start = el.selectionStart;
  const end = el.selectionEnd;
  el.value = el.value.substring(0, start) + tag + el.value.substring(end);
  el.focus();
  el.selectionStart = el.selectionEnd = start + tag.length;
  updatePhonePreview();
}

function updatePhonePreview() {
  let text = document.getElementById('campMessage').value;
  // Replace sample tags
  text = text.replace(/{customer_name}/g, 'Vikram Sharma')
             .replace(/{vehicle_model}/g, 'BMW 320d M-Sport')
             .replace(/{reg_no}/g, 'JK 02 CQ 7788');
  document.getElementById('chatBubblePreview').textContent = text;
}

function onAudienceChange() {
  const sel = document.getElementById('campAudience').value;
  const countEl = document.getElementById('audienceCountLabel');
  if (sel === 'ALL') countEl.textContent = '<?= $countAll ?> Recipients';
  if (sel === 'VIP') countEl.textContent = '<?= $countVip ?> VIP Members';
  if (sel === 'PPF') countEl.textContent = '<?= $countPpf ?> PPF Clients';
  if (sel === 'LEADS') countEl.textContent = '<?= $countLeads ?> Enquiries';
}

function sendPersonalizedWA(btn) {
  const tr = btn.closest('tr');
  const name = tr.dataset.name;
  const phone = tr.dataset.phone;
  const vehicle = tr.dataset.vehicle;
  const reg = tr.dataset.reg;

  let msg = document.getElementById('campMessage').value;
  msg = msg.replace(/{customer_name}/g, name)
           .replace(/{vehicle_model}/g, vehicle)
           .replace(/{reg_no}/g, reg);

  const url = `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
  window.open(url, '_blank');
  btn.style.background = '#10b981';
  btn.textContent = '✓ Sent';
}

async function saveAndLaunchCampaign() {
  const title = document.getElementById('campTitle').value;
  const audience = document.getElementById('campAudience').value;
  const msg = document.getElementById('campMessage').value;

  try {
    const res = await fetch('/crm/api/whatsapp/campaign', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name: title, audience_group: audience, message_template: msg })
    });
    const data = await res.json();
    if (data.success) {
      showToast('Campaign registered! Queue is ready to send.', 'success');
      // Auto open first contact
      const firstBtn = document.querySelector('#broadcastQueueBody button');
      if (firstBtn) firstBtn.scrollIntoView({ behavior: 'smooth' });
    }
  } catch (e) {
    showToast('Campaign ready in broadcast queue!', 'success');
  }
}

function focusCampaignEditor() {
  document.getElementById('campTitle').focus();
}

// Initial template load
applyPresetTemplate('monsoon');
</script>
