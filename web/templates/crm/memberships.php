<?php
// THE DRIVE CLINIC — CRM Memberships Management View
$members = Database::query("SELECT * FROM memberships ORDER BY created_at DESC");

$totalActive = 0;
$totalMRR = 0;
$expiringCount = 0;
$totalWashesLeft = 0;

foreach ($members as $m) {
  if ($m['status'] === 'ACTIVE') {
    $totalActive++;
    $totalMRR += ($m['billing_cycle'] === 'Monthly' ? (float)$m['price'] : ((float)$m['price'] / 12));
    $totalWashesLeft += (int)$m['washes_remaining'];
  }
  if ($m['status'] === 'EXPIRED' || strtotime($m['expiry_date']) < strtotime('+14 days')) {
    $expiringCount++;
  }
}
?>
<div class="crm-memberships-page">
  <!-- Page Header Row -->
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">BUSINESS &bull; VIP CLUB PROGRAM</div>
      <h1 class="crm-page-heading">Studio Club Memberships</h1>
      <p class="crm-page-sub">Manage enrolled club members, monthly wash allowances, ceramic passes, and renewals.</p>
    </div>
    <div class="crm-page-actions">
      <a href="/crm/export/memberships" class="crm-btn crm-btn-secondary">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        <span>Export Members CSV</span>
      </a>
      <button class="crm-btn crm-btn-primary" onclick="openEnrollMemberModal()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        <span>+ Enroll New Member</span>
      </button>
    </div>
  </div>

  <!-- Metric KPI Cards -->
  <div class="crm-stats-grid">
    <div class="crm-stat-card">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Active Club Members</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        </div>
      </div>
      <div class="crm-stat-value"><?= $totalActive ?> Members</div>
      <div class="crm-stat-footer">Recurring VIP clientele</div>
    </div>

    <div class="crm-stat-card">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Monthly Recurring Value</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        </div>
      </div>
      <div class="crm-stat-value">₹<?= number_format(round($totalMRR)) ?></div>
      <div class="crm-stat-footer">Normalized monthly subscription rate</div>
    </div>

    <div class="crm-stat-card">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Remaining Wash Credits</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
      </div>
      <div class="crm-stat-value"><?= $totalWashesLeft ?> Washes</div>
      <div class="crm-stat-footer">Available across enrolled vehicles</div>
    </div>

    <div class="crm-stat-card <?= $expiringCount > 0 ? 'crm-stat-warning' : '' ?>">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Renewal Due / Expired</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path></svg>
        </div>
      </div>
      <div class="crm-stat-value"><?= $expiringCount ?> <?= $expiringCount === 1 ? 'Member' : 'Members' ?></div>
      <div class="crm-stat-footer">Reminders sent via WhatsApp</div>
    </div>
  </div>

  <!-- Search & Filter Bar -->
  <div class="crm-tabs-row">
    <div class="crm-search-input-wrap" style="flex:1; max-width: 400px;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" id="memberSearch" class="crm-search-input" placeholder="Search Member, Phone, Vehicle Plate..." oninput="filterMembersTable()">
    </div>
    <select id="memberPlanFilter" class="crm-select crm-select-sm" onchange="filterMembersTable()">
      <option value="">All Club Plans</option>
      <option value="Signature Wash Club">Signature Wash Club</option>
      <option value="Ceramic Care Pass">Ceramic Care Pass</option>
      <option value="Elite Concierge Detail Pass">Elite Concierge Detail Pass</option>
    </select>
  </div>

  <!-- Members Directory Table -->
  <div class="crm-card">
    <div class="crm-table-container">
      <table class="crm-table" id="membersTable">
        <thead>
          <tr>
            <th>Member Pass #</th>
            <th>Client Name & Contact</th>
            <th>Registered Vehicle</th>
            <th>Club Plan</th>
            <th>Wash Allowance Left</th>
            <th>Validity & Expiry</th>
            <th>Status</th>
            <th>Quick Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($members as $m): 
            $isActive = ($m['status'] === 'ACTIVE');
            $washPct = ($m['washes_total'] > 0) ? round(($m['washes_remaining'] / $m['washes_total']) * 100) : 100;
          ?>
          <tr class="crm-member-row" data-search="<?= htmlspecialchars(strtolower($m['customer_name'] . ' ' . $m['phone'] . ' ' . $m['vehicle_model'] . ' ' . ($m['vehicle_reg'] ?? '') . ' ' . $m['member_no'])) ?>" data-plan="<?= htmlspecialchars($m['plan_name']) ?>">
            <td>
              <strong class="crm-ref-code" style="color:var(--crm-red);"><?= htmlspecialchars($m['member_no']) ?></strong>
              <div style="font-size:10px; color:#6b7280;"><?= $m['billing_cycle'] ?> billing</div>
            </td>
            <td>
              <strong><?= htmlspecialchars($m['customer_name']) ?></strong>
              <div class="crm-whatsapp-indicator">
                <a href="https://wa.me/<?= preg_replace('/\D/', '', $m['phone']) ?>" target="_blank" class="crm-wa-link">
                  💬 <?= htmlspecialchars($m['phone']) ?>
                </a>
              </div>
            </td>
            <td>
              <strong><?= htmlspecialchars($m['vehicle_model']) ?></strong>
              <div class="crm-plate-tag"><?= htmlspecialchars($m['vehicle_reg'] ?: 'JK 02 XX 0000') ?></div>
            </td>
            <td>
              <span class="crm-pill <?= str_contains($m['plan_name'], 'Ceramic') ? 'crm-pill-purple' : (str_contains($m['plan_name'], 'Concierge') ? 'crm-pill-confirmed' : 'crm-pill-info') ?>">
                <?= htmlspecialchars($m['plan_name']) ?>
              </span>
              <div style="font-size:11px; color:#4b5563; font-weight:700; margin-top:2px;">₹<?= number_format((float)$m['price']) ?> / <?= $m['billing_cycle'] ?></div>
            </td>
            <td>
              <div class="crm-stock-meter-wrap" style="width:120px;">
                <div class="crm-stock-qty-row">
                  <strong class="crm-qty-num <?= $m['washes_remaining'] == 0 ? 'crm-text-red' : '' ?>">
                    <?= $m['washes_remaining'] ?> / <?= $m['washes_total'] ?> Washes
                  </strong>
                </div>
                <div class="crm-meter-bar">
                  <div class="crm-meter-fill <?= $m['washes_remaining'] == 0 ? 'crm-meter-warning' : 'crm-meter-healthy' ?>" style="width: <?= $washPct ?>%;"></div>
                </div>
              </div>
            </td>
            <td>
              <div class="crm-time-stamp">
                <strong>Until <?= date('d M Y', strtotime($m['expiry_date'])) ?></strong>
                <span style="color:<?= $isActive ? '#059669' : '#dc2626' ?>;">
                  <?= $isActive ? ('Started ' . date('d M Y', strtotime($m['start_date']))) : 'Membership Expired' ?>
                </span>
              </div>
            </td>
            <td>
              <?php if ($isActive): ?>
                <span class="crm-pill crm-pill-success">Active</span>
              <?php else: ?>
                <span class="crm-pill crm-pill-danger">Expired</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="crm-action-group">
                <?php if ($isActive && $m['washes_remaining'] > 0): ?>
                  <button class="crm-btn crm-btn-xs crm-btn-primary" onclick="redeemWash(<?= $m['id'] ?>, '<?= addslashes($m['customer_name']) ?>')">
                    💧 Redeem Wash
                  </button>
                <?php else: ?>
                  <button class="crm-btn crm-btn-xs crm-btn-success" onclick="renewMembership(<?= $m['id'] ?>, '<?= addslashes($m['customer_name']) ?>', '<?= addslashes($m['plan_name']) ?>')">
                    🔄 Renew Plan
                  </button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Enroll Member Modal -->
<div class="crm-modal-backdrop" id="modalEnrollMember">
  <div class="crm-modal crm-modal-md">
    <div class="crm-modal-header">
      <div class="crm-modal-title-group">
        <h3 class="crm-modal-title">Enroll Studio Club Member</h3>
        <p class="crm-modal-sub">Register vehicle owner into recurring wash or ceramic care program.</p>
      </div>
      <button class="crm-modal-close" onclick="closeModal('modalEnrollMember')">&times;</button>
    </div>
    <form id="formEnrollMember" onsubmit="handleCrmEnrollMember(event)">
      <div class="crm-modal-body">
        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Customer Name *</label>
            <input type="text" name="customer_name" class="crm-input" placeholder="e.g. Vikramaditya Sharma" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Phone / WhatsApp *</label>
            <input type="tel" name="phone" class="crm-input" placeholder="e.g. +91 94191 22334" required>
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Vehicle Brand & Model *</label>
            <input type="text" name="vehicle_model" class="crm-input" placeholder="e.g. Audi A6 / Thar Roxx" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Registration Plate</label>
            <input type="text" name="vehicle_reg" class="crm-input" placeholder="e.g. JK 02 AB 1234">
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Club Membership Plan *</label>
            <select name="plan_name" id="crmPlanSelect" class="crm-select" required onchange="autoFillPlanDetails(this)">
              <option value="Signature Wash Club" data-price="2999" data-cycle="Monthly" data-washes="3">Signature Wash Club (₹2,999/mo - 3 Washes)</option>
              <option value="Ceramic Care Pass" data-price="14999" data-cycle="Annual" data-washes="6">Ceramic Care Pass (₹14,999/yr - 2 Top-ups + 6 Washes)</option>
              <option value="Elite Concierge Detail Pass" data-price="29999" data-cycle="Annual" data-washes="12">Elite Concierge Detail Pass (₹29,999/yr - Unlimited)</option>
            </select>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Billing Cycle *</label>
            <select name="billing_cycle" id="crmBillingCycle" class="crm-select" required>
              <option value="Monthly">Monthly</option>
              <option value="Annual">Annual</option>
            </select>
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Membership Price (₹) *</label>
            <input type="number" step="100" name="price" id="crmPlanPrice" class="crm-input" value="2999" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Total Wash Allowance *</label>
            <input type="number" name="washes_total" id="crmPlanWashes" class="crm-input" value="3" required>
          </div>
        </div>

        <div class="crm-form-group">
          <label class="crm-label">Perks & Warranty Notes</label>
          <textarea name="perks_notes" class="crm-textarea" placeholder="Note special requests, valet pickup address, or coating brand"></textarea>
        </div>
      </div>
      <div class="crm-modal-footer">
        <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalEnrollMember')">Cancel</button>
        <button type="submit" class="crm-btn crm-btn-primary">Activate Member Pass</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEnrollMemberModal() {
  openModal('modalEnrollMember');
}

function autoFillPlanDetails(sel) {
  const opt = sel.options[sel.selectedIndex];
  if (!opt) return;
  document.getElementById('crmPlanPrice').value = opt.dataset.price || 2999;
  document.getElementById('crmBillingCycle').value = opt.dataset.cycle || 'Monthly';
  document.getElementById('crmPlanWashes').value = opt.dataset.washes || 3;
}

function filterMembersTable() {
  const q = (document.getElementById('memberSearch')?.value || '').toLowerCase().trim();
  const plan = document.getElementById('memberPlanFilter')?.value || '';
  const rows = document.querySelectorAll('.crm-member-row');

  rows.forEach(r => {
    const text = (r.dataset.search || '').toLowerCase();
    const rowPlan = r.dataset.plan || '';
    const matchesQ = !q || text.includes(q);
    const matchesPlan = !plan || rowPlan === plan;
    r.style.display = (matchesQ && matchesPlan) ? '' : 'none';
  });
}

async function handleCrmEnrollMember(e) {
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
      showToast(data.message || 'Member pass activated successfully!', 'success');
      closeModal('modalEnrollMember');
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(data.error || 'Failed to enroll member', 'danger');
    }
  } catch (err) {
    showToast('Network error activating membership', 'danger');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Activate Member Pass';
  }
}

async function redeemWash(id, name) {
  if (!confirm(`Redeem 1 Wash pass for ${name}? Remaining washes will be decremented.`)) return;
  try {
    const res = await fetch('/crm/api/memberships/redeem', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ member_id: id })
    });
    const data = await res.json();
    if (data.success) {
      showToast(data.message || '1 Wash redeemed!', 'success');
      setTimeout(() => location.reload(), 700);
    } else {
      showToast(data.error || 'Could not redeem wash', 'danger');
    }
  } catch (err) {
    showToast('Network error redeeming wash', 'danger');
  }
}

async function renewMembership(id, name, plan) {
  if (!confirm(`Renew ${plan} for ${name}? Validity will be extended.`)) return;
  try {
    const res = await fetch('/crm/api/memberships/renew', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ member_id: id })
    });
    const data = await res.json();
    if (data.success) {
      showToast(data.message || 'Membership renewed!', 'success');
      setTimeout(() => location.reload(), 700);
    }
  } catch (err) {
    showToast('Network error renewing membership', 'danger');
  }
}
</script>
