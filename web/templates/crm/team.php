<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Technicians & Workshop Payroll
$staff = Database::query("SELECT * FROM staff ORDER BY status ASC, id ASC");
$payouts = Database::query("SELECT * FROM salary_payouts ORDER BY payment_date DESC, id DESC");

// Payroll totals
$totalBaseSalary = 0.0;
foreach ($staff as $s) {
    if ($s['status'] === 'ACTIVE') {
        $totalBaseSalary += (float)$s['base_salary'];
    }
}

$totalPaidThisMonth = 0.0;
$currentMonth = date('F Y');
foreach ($payouts as $p) {
    if (strpos($p['month_name'], date('F')) !== false) {
        $totalPaidThisMonth += (float)$p['net_paid'];
    }
}
?>

<div class="crm-team-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">OPERATIONS &bull; WORKSHOP CREW</div>
      <h1 class="crm-page-heading">Technicians & Workshop Payroll</h1>
      <p class="crm-page-sub">Manage master PPF installers, paint correction technicians, monthly salary disbursements, job-card incentives, and pay slips.</p>
    </div>
    <div class="crm-page-actions" style="display:flex; gap:10px;">
      <button class="crm-btn crm-btn-secondary" onclick="openModal('modalAddStaff')">+ Add Staff Member</button>
      <button class="crm-btn crm-btn-primary" onclick="openModal('modalAddSalary')">+ Record Salary Payout</button>
    </div>
  </div>

  <!-- KPI Metrics -->
  <div class="crm-kpi-grid">
    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Active Workshop Crew</span>
        <span class="crm-kpi-icon" style="color:#38bdf8;">👥</span>
      </div>
      <div class="crm-kpi-value"><?= count(array_filter($staff, fn($s) => $s['status'] === 'ACTIVE')) ?></div>
      <div class="crm-kpi-change positive">Certified PPF & Detailing Detailers</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Monthly Base Payroll</span>
        <span class="crm-kpi-icon" style="color:#f59e0b;">💼</span>
      </div>
      <div class="crm-kpi-value">₹<?= number_format($totalBaseSalary) ?></div>
      <div class="crm-kpi-change neutral">Fixed Monthly Base Allocation</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Salary Paid This Month</span>
        <span class="crm-kpi-icon" style="color:#10b981;">✅</span>
      </div>
      <div class="crm-kpi-value" style="color:#10b981;">₹<?= number_format($totalPaidThisMonth) ?></div>
      <div class="crm-kpi-change positive"><?= date('F Y') ?> Disbursed Total</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Job Card Commission Pool</span>
        <span class="crm-kpi-icon" style="color:#e11d48;">🏆</span>
      </div>
      <div class="crm-kpi-value" style="color:var(--red);">₹1,000 – ₹1,500</div>
      <div class="crm-kpi-change positive">Per Completed PPF / Ceramic Wrap</div>
    </div>
  </div>

  <!-- Tabs Navigation -->
  <div class="crm-card" style="margin-top:24px;">
    <div class="crm-card-header" style="border-bottom:1px solid var(--crm-border); padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
      <div style="display:flex; gap:8px;">
        <button class="crm-btn crm-btn-sm crm-btn-primary" id="btnTabStaff" onclick="showTeamTab('staff')">Technicians Directory (<?= count($staff) ?>)</button>
        <button class="crm-btn crm-btn-sm crm-btn-ghost" id="btnTabPayouts" onclick="showTeamTab('payouts')">Salary Payout Ledger (<?= count($payouts) ?>)</button>
      </div>
    </div>

    <!-- 1. Staff Directory Table -->
    <div id="teamTabStaff">
      <div class="crm-table-container">
        <table class="crm-table">
          <thead>
            <tr>
              <th>Technician / Detailer</th>
              <th>Specialization & Role</th>
              <th>Contact Number</th>
              <th>Base Salary</th>
              <th>Incentive / Job</th>
              <th>Status</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($staff as $s): ?>
            <tr>
              <td>
                <div style="display:flex; align-items:center; gap:10px;">
                  <div style="width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg, #e11d48, #9f1239); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px;">
                    <?= strtoupper(substr($s['name'], 0, 2)) ?>
                  </div>
                  <div>
                    <strong><?= htmlspecialchars($s['name']) ?></strong>
                    <div class="crm-text-muted" style="font-size:11px;">Joined: <?= date('M Y', strtotime($s['join_date'] ?: '2023-01-01')) ?></div>
                  </div>
                </div>
              </td>
              <td>
                <span class="crm-pill crm-pill-progress"><?= htmlspecialchars($s['role']) ?></span>
              </td>
              <td><?= htmlspecialchars($s['phone'] ?: '—') ?></td>
              <td><strong>₹<?= number_format((float)$s['base_salary']) ?> / mo</strong></td>
              <td><span style="color:#10b981; font-weight:700;">+ ₹<?= number_format((float)$s['incentive_per_job']) ?></span></td>
              <td>
                <?php if ($s['status'] === 'ACTIVE'): ?>
                  <span class="crm-pill crm-pill-success">Active Detailer</span>
                <?php else: ?>
                  <span class="crm-pill crm-pill-warning"><?= htmlspecialchars($s['status']) ?></span>
                <?php endif; ?>
              </td>
              <td style="text-align:right;">
                <button class="crm-btn crm-btn-xs crm-btn-primary" onclick="quickPayStaff(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['name'])) ?>', <?= (float)$s['base_salary'] ?>)">
                  Pay Salary
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 2. Salary Payouts Ledger Table -->
    <div id="teamTabPayouts" style="display:none;">
      <div class="crm-table-container">
        <table class="crm-table">
          <thead>
            <tr>
              <th>Payout Ref #</th>
              <th>Pay Month</th>
              <th>Technician Name</th>
              <th>Base Salary</th>
              <th>Job Incentives</th>
              <th>Net Disbursed (₹)</th>
              <th>Payment Mode</th>
              <th>Disbursement Date</th>
              <th>Status</th>
              <th style="text-align:right;">Pay Advice</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($payouts)): ?>
              <?php foreach ($payouts as $p): ?>
              <tr>
                <td><strong class="crm-ref-code"><?= htmlspecialchars($p['payout_no']) ?></strong></td>
                <td><strong><?= htmlspecialchars($p['month_name']) ?></strong></td>
                <td><?= htmlspecialchars($p['staff_name']) ?></td>
                <td>₹<?= number_format((float)$p['base_salary']) ?></td>
                <td style="color:#10b981;">+ ₹<?= number_format((float)$p['incentive_amount']) ?></td>
                <td>
                  <strong style="font-size:14px; color:#fff;">₹<?= number_format((float)$p['net_paid']) ?></strong>
                </td>
                <td><span class="crm-source-badge"><?= htmlspecialchars($p['payment_method']) ?></span></td>
                <td><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
                <td><span class="crm-pill crm-pill-success">Disbursed</span></td>
                <td style="text-align:right;">
                  <button class="crm-btn crm-btn-xs crm-btn-ghost" onclick="sendSalaryWhatsApp('<?= htmlspecialchars(addslashes($p['staff_name'])) ?>', '<?= htmlspecialchars($p['month_name']) ?>', '<?= number_format((float)$p['net_paid']) ?>', '<?= htmlspecialchars($p['payout_no']) ?>')">
                    💬 WhatsApp Slip
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="10" style="text-align:center; padding:36px; color:#888;">No salary payouts logged yet.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Add Staff Member -->
<div class="crm-modal-backdrop" id="modalAddStaff">
  <div class="crm-modal crm-modal-md">
    <div class="crm-modal-header">
      <div class="crm-modal-title-group">
        <h3 class="crm-modal-title">Add Workshop Technician / Staff</h3>
        <p class="crm-modal-sub">Register master detailers, PPF applicators, or workshop supervisors.</p>
      </div>
      <button class="crm-modal-close" onclick="closeModal('modalAddStaff')">&times;</button>
    </div>
    <form id="formAddStaff" onsubmit="handleCreateStaff(event)">
      <div class="crm-modal-body">
        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Full Name *</label>
            <input type="text" name="name" class="crm-input" placeholder="e.g. Gurpreet Singh" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Specialization / Role *</label>
            <select name="role" class="crm-select" required>
              <option value="Master PPF Applicator">Master PPF Applicator</option>
              <option value="Senior Paint Correction Specialist">Senior Paint Correction Specialist</option>
              <option value="Ceramic & Graphene Specialist">Ceramic & Graphene Specialist</option>
              <option value="Decontamination & Steam Lead">Decontamination & Steam Lead</option>
              <option value="Bike Detailing Specialist">Bike Detailing Specialist</option>
              <option value="Bay Quality Inspector">Bay Quality Inspector</option>
            </select>
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Mobile Phone Number *</label>
            <input type="tel" name="phone" class="crm-input" placeholder="+91 94191 XXXXX" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Email Address</label>
            <input type="email" name="email" class="crm-input" placeholder="staff@thedriveclinic.com">
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Monthly Base Salary (₹) *</label>
            <input type="number" step="100" min="5000" name="base_salary" class="crm-input" placeholder="e.g. 30000" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Job Card Incentive (₹ per car) *</label>
            <input type="number" step="50" min="0" name="incentive_per_job" class="crm-input" value="1000" required>
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Joining Date</label>
            <input type="date" name="join_date" class="crm-input" value="<?= date('Y-m-d') ?>">
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Employment Status</label>
            <select name="status" class="crm-select">
              <option value="ACTIVE">Active & Working</option>
              <option value="PROBATION">Probation Period</option>
              <option value="LEAVE">On Leave</option>
            </select>
          </div>
        </div>
      </div>
      <div class="crm-modal-footer">
        <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalAddStaff')">Cancel</button>
        <button type="submit" class="crm-btn crm-btn-primary">Register Technician</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Record Salary Payout -->
<div class="crm-modal-backdrop" id="modalAddSalary">
  <div class="crm-modal crm-modal-md">
    <div class="crm-modal-header">
      <div class="crm-modal-title-group">
        <h3 class="crm-modal-title">Record Monthly Salary Disbursement</h3>
        <p class="crm-modal-sub">Calculate base salary, completed job card commissions, and deduct advances.</p>
      </div>
      <button class="crm-modal-close" onclick="closeModal('modalAddSalary')">&times;</button>
    </div>
    <form id="formAddSalary" onsubmit="handleCreateSalary(event)">
      <div class="crm-modal-body">
        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Select Staff Member *</label>
            <select name="staff_id" id="salaryStaffSelect" class="crm-select" required onchange="onSalaryStaffChange(this)">
              <option value="">-- Choose Staff Member --</option>
              <?php foreach ($staff as $st): ?>
                <option value="<?= $st['id'] ?>" data-name="<?= htmlspecialchars($st['name']) ?>" data-base="<?= (float)$st['base_salary'] ?>">
                  <?= htmlspecialchars($st['name']) ?> (<?= htmlspecialchars($st['role']) ?> &bull; Base ₹<?= number_format((float)$st['base_salary']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
            <input type="hidden" name="staff_name" id="salaryStaffName">
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Payroll Month *</label>
            <input type="text" name="month_name" class="crm-input" value="<?= date('F Y') ?>" required>
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Base Salary (₹) *</label>
            <input type="number" step="0.01" name="base_salary" id="salaryBaseInput" class="crm-input" placeholder="0" required oninput="calculateNetSalary()">
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Job Card Commission / Incentives (₹)</label>
            <input type="number" step="0.01" name="incentive_amount" id="salaryIncentiveInput" class="crm-input" value="0" oninput="calculateNetSalary()">
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Bonus / Festival Extra (₹)</label>
            <input type="number" step="0.01" name="bonus" id="salaryBonusInput" class="crm-input" value="0" oninput="calculateNetSalary()">
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Deductions / Advance Returned (₹)</label>
            <input type="number" step="0.01" name="deductions" id="salaryDeductionInput" class="crm-input" value="0" oninput="calculateNetSalary()">
          </div>
        </div>

        <div style="background:rgba(16,185,129,0.1); border:1px solid #10b981; border-radius:8px; padding:14px 18px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
          <span style="font-weight:700; color:#fff;">Net Disbursement Total:</span>
          <span style="font-size:20px; font-weight:800; color:#10b981;" id="salaryNetDisplay">₹0</span>
          <input type="hidden" name="net_paid" id="salaryNetInput">
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Disbursement Mode *</label>
            <select name="payment_method" class="crm-select" required>
              <option value="UPI">UPI / GPay Transfer</option>
              <option value="Bank Wire">Bank Wire / IMPS</option>
              <option value="Cash">Cash in Hand</option>
            </select>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Payment Date *</label>
            <input type="date" name="payment_date" class="crm-input" value="<?= date('Y-m-d') ?>" required>
          </div>
        </div>

        <div class="crm-form-group">
          <label class="crm-label">Voucher / Pay Notes</label>
          <textarea name="notes" class="crm-textarea" placeholder="e.g. October 2026 salary + 4 PPF wraps bonus"></textarea>
        </div>
      </div>
      <div class="crm-modal-footer">
        <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalAddSalary')">Cancel</button>
        <button type="submit" class="crm-btn crm-btn-success">Confirm & Disburse Salary</button>
      </div>
    </form>
  </div>
</div>

<script>
function showTeamTab(tab) {
  document.getElementById('teamTabStaff').style.display = tab === 'staff' ? 'block' : 'none';
  document.getElementById('teamTabPayouts').style.display = tab === 'payouts' ? 'block' : 'none';
  document.getElementById('btnTabStaff').className = tab === 'staff' ? 'crm-btn crm-btn-sm crm-btn-primary' : 'crm-btn crm-btn-sm crm-btn-ghost';
  document.getElementById('btnTabPayouts').className = tab === 'payouts' ? 'crm-btn crm-btn-sm crm-btn-primary' : 'crm-btn crm-btn-sm crm-btn-ghost';
}

function onSalaryStaffChange(sel) {
  const opt = sel.options[sel.selectedIndex];
  if (opt && opt.dataset.base) {
    document.getElementById('salaryBaseInput').value = opt.dataset.base;
    document.getElementById('salaryStaffName').value = opt.dataset.name;
    calculateNetSalary();
  }
}

function quickPayStaff(id, name, base) {
  openModal('modalAddSalary');
  const sel = document.getElementById('salaryStaffSelect');
  sel.value = id;
  document.getElementById('salaryStaffName').value = name;
  document.getElementById('salaryBaseInput').value = base;
  calculateNetSalary();
}

function calculateNetSalary() {
  const base = parseFloat(document.getElementById('salaryBaseInput').value || 0);
  const inc = parseFloat(document.getElementById('salaryIncentiveInput').value || 0);
  const bon = parseFloat(document.getElementById('salaryBonusInput').value || 0);
  const ded = parseFloat(document.getElementById('salaryDeductionInput').value || 0);

  const net = Math.max(0, (base + inc + bon) - ded);
  document.getElementById('salaryNetDisplay').textContent = '₹' + net.toLocaleString('en-IN');
  document.getElementById('salaryNetInput').value = net;
}

async function handleCreateStaff(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;

  try {
    const res = await fetch('/crm/api/staff/create', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
      showToast('Staff member registered successfully!', 'success');
      closeModal('modalAddStaff');
      setTimeout(() => location.reload(), 600);
    } else {
      showToast(data.error || 'Failed to add staff', 'danger');
    }
  } catch (err) {
    showToast('Network error registering staff', 'danger');
  } finally {
    btn.disabled = false;
  }
}

async function handleCreateSalary(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;

  try {
    const res = await fetch('/crm/api/salary/create', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
      showToast('Salary disbursement logged successfully!', 'success');
      closeModal('modalAddSalary');
      setTimeout(() => location.reload(), 600);
    } else {
      showToast(data.error || 'Failed to record salary payout', 'danger');
    }
  } catch (err) {
    showToast('Network error recording salary', 'danger');
  } finally {
    btn.disabled = false;
  }
}

function sendSalaryWhatsApp(name, month, net, ref) {
  const text = encodeURIComponent(`💼 *THE DRIVE CLINIC — SALARY DISBURSEMENT SLIP* 💼\n\nDear ${name},\nYour salary for *${month}* has been successfully processed.\n\n💰 *Net Amount Paid:* ₹${net}\n📜 *Voucher Ref:* ${ref}\n\nThank you for your dedicated detailing craftsmanship at The Drive Clinic Jammu!`);
  window.open(`https://wa.me/?text=${text}`, '_blank');
}
</script>
