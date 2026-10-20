<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Payments & Operating Expense Ledger
$expenses = Database::query("SELECT * FROM expenses ORDER BY date DESC, id DESC");
$invoicesPaid = Database::query("
    SELECT i.*, c.name as customer_name, v.brand, v.model, v.registration
    FROM invoices i
    LEFT JOIN customers c ON i.customer_id = c.id
    LEFT JOIN vehicles v ON i.vehicle_id = v.id
    WHERE i.payment_status = 'PAID' OR i.paid_amount > 0
    ORDER BY i.created_at DESC
");
$invoicesPending = Database::query("
    SELECT i.*, c.name as customer_name
    FROM invoices i
    LEFT JOIN customers c ON i.customer_id = c.id
    WHERE i.payment_status != 'PAID'
");

// Calculations
$totalExpenses = 0.0;
foreach ($expenses as $e) {
    $totalExpenses += (float)$e['amount'];
}

$totalCollected = 0.0;
foreach ($invoicesPaid as $p) {
    $totalCollected += (float)($p['paid_amount'] ?: $p['grand_total']);
}

$totalPending = 0.0;
foreach ($invoicesPending as $pend) {
    $totalPending += max(0, (float)$pend['grand_total'] - (float)$pend['paid_amount']);
}

$netBalance = $totalCollected - $totalExpenses;
?>

<div class="crm-payments-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">FINANCE &bull; GENERAL LEDGER</div>
      <h1 class="crm-page-heading">Payments & Operating Expense Ledger</h1>
      <p class="crm-page-sub">Comprehensive audit of customer incoming collections (UPI/Cash/POS), studio operating expenses, supplier purchases, and net workshop cashflow.</p>
    </div>
    <div class="crm-page-actions" style="display:flex; gap:10px;">
      <button class="crm-btn crm-btn-secondary" onclick="openModal('modalAddPayment')">+ Record Collection</button>
      <button class="crm-btn crm-btn-primary" onclick="openModal('modalAddExpense')">+ Record Expense</button>
    </div>
  </div>

  <!-- Financial KPI Metrics -->
  <div class="crm-kpi-grid">
    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Total Customer Collections</span>
        <span class="crm-kpi-icon" style="color:#10b981;">💰</span>
      </div>
      <div class="crm-kpi-value" style="color:#10b981;">₹<?= number_format($totalCollected) ?></div>
      <div class="crm-kpi-change positive">UPI, Cash & Card Inflow</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Total Operating Expenses</span>
        <span class="crm-kpi-icon" style="color:#ef4444;">📉</span>
      </div>
      <div class="crm-kpi-value" style="color:#ef4444;">₹<?= number_format($totalExpenses) ?></div>
      <div class="crm-kpi-change negative">Rent, Rolls, Power & Consumables</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Net Studio Cash Balance</span>
        <span class="crm-kpi-icon" style="color:#38bdf8;">⚖️</span>
      </div>
      <div class="crm-kpi-value" style="color:<?= $netBalance >= 0 ? '#38bdf8' : '#f43f5e' ?>;">
        ₹<?= number_format($netBalance) ?>
      </div>
      <div class="crm-kpi-change <?= $netBalance >= 0 ? 'positive' : 'negative' ?>">
        <?= $netBalance >= 0 ? 'Operating Cash Surplus' : 'Deficit / Re-investment Phase' ?>
      </div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Unpaid Receivables</span>
        <span class="crm-kpi-icon" style="color:#f59e0b;">⏳</span>
      </div>
      <div class="crm-kpi-value" style="color:#f59e0b;">₹<?= number_format($totalPending) ?></div>
      <div class="crm-kpi-change neutral"><?= count($invoicesPending) ?> Pending Invoices</div>
    </div>
  </div>

  <!-- Interactive Ledger Tabs -->
  <div class="crm-card" style="margin-top:24px;">
    <div class="crm-card-header" style="border-bottom:1px solid var(--crm-border); padding:16px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div style="display:flex; gap:8px;" id="ledgerTabs">
        <button class="crm-btn crm-btn-sm crm-btn-primary" onclick="switchLedgerTab('expenses', this)">Operating Expenses (<?= count($expenses) ?>)</button>
        <button class="crm-btn crm-btn-sm crm-btn-ghost" onclick="switchLedgerTab('collections', this)">Incoming Payments (<?= count($invoicesPaid) ?>)</button>
        <button class="crm-btn crm-btn-sm crm-btn-ghost" onclick="switchLedgerTab('breakdown', this)">Expense Categories P&L</button>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <input type="text" id="ledgerSearchInput" placeholder="Filter voucher, vendor or notes..." class="crm-input" style="width:240px; padding:6px 12px; font-size:12px;" oninput="filterLedgerTable()">
      </div>
    </div>

    <!-- 1. Operating Expenses Tab -->
    <div id="tab-expenses" class="ledger-tab-panel">
      <div class="crm-table-container">
        <table class="crm-table" id="expensesTable">
          <thead>
            <tr>
              <th>Voucher / Ref #</th>
              <th>Date</th>
              <th>Category</th>
              <th>Expense Description</th>
              <th>Paid To / Vendor</th>
              <th>Mode</th>
              <th>Amount (₹)</th>
              <th style="text-align:right;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($expenses)): ?>
              <?php foreach ($expenses as $ex): ?>
              <tr>
                <td><strong class="crm-ref-code"><?= htmlspecialchars($ex['expense_no']) ?></strong></td>
                <td><?= date('d M Y', strtotime($ex['date'])) ?></td>
                <td>
                  <span class="crm-pill crm-pill-warning"><?= htmlspecialchars($ex['category']) ?></span>
                </td>
                <td>
                  <strong><?= htmlspecialchars($ex['title']) ?></strong>
                  <?php if (!empty($ex['notes'])): ?>
                    <div class="crm-text-muted" style="font-size:11px;"><?= htmlspecialchars($ex['notes']) ?></div>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($ex['paid_to'] ?: '—') ?></td>
                <td>
                  <span class="crm-source-badge"><?= htmlspecialchars($ex['payment_method']) ?></span>
                </td>
                <td><strong style="color:#ef4444; font-size:14px;">- ₹<?= number_format((float)$ex['amount'], 2) ?></strong></td>
                <td style="text-align:right;">
                  <button class="crm-btn crm-btn-xs crm-btn-danger" onclick="deleteExpense(<?= $ex['id'] ?>)">Delete</button>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" style="text-align:center; padding:36px; color:#888;">No expenses recorded yet. Click "+ Record Expense" above to add.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 2. Incoming Payments Tab -->
    <div id="tab-collections" class="ledger-tab-panel" style="display:none;">
      <div class="crm-table-container">
        <table class="crm-table">
          <thead>
            <tr>
              <th>Invoice #</th>
              <th>Settlement Date</th>
              <th>Customer & Contact</th>
              <th>Vehicle Specification</th>
              <th>Payment Mode</th>
              <th>Settled Amount (₹)</th>
              <th style="text-align:right;">Invoice Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($invoicesPaid)): ?>
              <?php foreach ($invoicesPaid as $inv): ?>
              <tr>
                <td><strong class="crm-ref-code"><?= htmlspecialchars($inv['invoice_no']) ?></strong></td>
                <td><?= date('d M Y', strtotime($inv['created_at'])) ?></td>
                <td>
                  <strong><?= htmlspecialchars($inv['customer_name'] ?? 'Walk-in') ?></strong>
                </td>
                <td>
                  <?= htmlspecialchars(($inv['brand'] ?? '') . ' ' . ($inv['model'] ?? 'Vehicle')) ?>
                  <span class="crm-text-muted">(<?= htmlspecialchars($inv['registration'] ?? 'JK 02') ?>)</span>
                </td>
                <td>
                  <span class="crm-pill crm-pill-success"><?= htmlspecialchars($inv['payment_method'] ?: 'UPI') ?></span>
                </td>
                <td>
                  <strong style="color:#10b981; font-size:14px;">+ ₹<?= number_format((float)$inv['paid_amount'] ?: (float)$inv['grand_total'], 2) ?></strong>
                </td>
                <td style="text-align:right;">
                  <a href="/crm/invoices/<?= $inv['id'] ?>/print" target="_blank" class="crm-btn crm-btn-xs crm-btn-ghost">
                    🖨️ Receipt PDF
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" style="text-align:center; padding:36px; color:#888;">No customer settlements logged yet.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 3. Expense Categories Breakdown P&L -->
    <div id="tab-breakdown" class="ledger-tab-panel" style="display:none; padding:24px;">
      <?php
      $catTotals = [];
      foreach ($expenses as $e) {
          $c = $e['category'];
          $catTotals[$c] = ($catTotals[$c] ?? 0) + (float)$e['amount'];
      }
      arsort($catTotals);
      ?>
      <h3 style="color:#fff; font-size:18px; margin-bottom:16px;">Expense Outflow by Department</h3>
      <div style="display:flex; flex-direction:column; gap:16px; max-width:700px;">
        <?php foreach ($catTotals as $catName => $catSum): 
          $pct = $totalExpenses > 0 ? round(($catSum / $totalExpenses) * 100, 1) : 0;
        ?>
          <div>
            <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px;">
              <span style="color:#fff; font-weight:600;"><?= htmlspecialchars($catName) ?></span>
              <span style="color:#ef4444; font-weight:700;">₹<?= number_format($catSum, 2) ?> (<?= $pct ?>%)</span>
            </div>
            <div style="height:8px; background:rgba(255,255,255,0.08); border-radius:4px; overflow:hidden;">
              <div style="height:100%; width:<?= $pct ?>%; background:linear-gradient(90deg, #E11D48, #F43F5E); border-radius:4px;"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Record Expense -->
<div class="crm-modal-backdrop" id="modalAddExpense">
  <div class="crm-modal crm-modal-md">
    <div class="crm-modal-header">
      <div class="crm-modal-title-group">
        <h3 class="crm-modal-title">Record Operating Expense</h3>
        <p class="crm-modal-sub">Log studio bills, chemical purchases, equipment maintenance, or rent.</p>
      </div>
      <button class="crm-modal-close" onclick="closeModal('modalAddExpense')">&times;</button>
    </div>
    <form id="formAddExpense" onsubmit="handleCreateExpense(event)">
      <div class="crm-modal-body">
        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Expense Category *</label>
            <select name="category" class="crm-select" required>
              <option value="Workshop Rent">Workshop Rent & Lease</option>
              <option value="PPF & Film Rolls">PPF & Vinyl Film Rolls</option>
              <option value="Chemicals & Consumables">Detailing Chemicals & Pastes</option>
              <option value="Electricity & Power">Electricity, GenSet & Utilities</option>
              <option value="Microfibers & Pads">Pads, Towels & Accessories</option>
              <option value="Equipment Maintenance">Machinery & Tool Maintenance</option>
              <option value="Staff Welfare & Meals">Staff Refreshments & Food</option>
              <option value="Marketing & Ads">Marketing, Meta Ads & Printing</option>
              <option value="Office & Misc">Office Supplies & Miscellaneous</option>
            </select>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Amount (₹) *</label>
            <input type="number" step="0.01" min="1" name="amount" class="crm-input" placeholder="e.g. 15000" required>
          </div>
        </div>

        <div class="crm-form-group">
          <label class="crm-label">Expense Title / Description *</label>
          <input type="text" name="title" class="crm-input" placeholder="e.g. Koch Chemie H9.02 Heavy Cut restock (5L)" required>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Paid To / Vendor</label>
            <input type="text" name="paid_to" class="crm-input" placeholder="e.g. AutoCare Distributors Delhi">
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Payment Method *</label>
            <select name="payment_method" class="crm-select" required>
              <option value="UPI">UPI / GPay / PhonePe</option>
              <option value="Bank Transfer">Bank NEFT / RTGS</option>
              <option value="Cash">Cash in Hand</option>
              <option value="Card">Card Swipe</option>
            </select>
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Expense Date *</label>
            <input type="date" name="date" class="crm-input" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Bill / Receipt / Voucher #</label>
            <input type="text" name="receipt_no" class="crm-input" placeholder="e.g. INV-9912 or UPI-7782">
          </div>
        </div>

        <div class="crm-form-group">
          <label class="crm-label">Notes & Remarks</label>
          <textarea name="notes" class="crm-textarea" placeholder="Optional notes for accounting ledger..."></textarea>
        </div>
      </div>
      <div class="crm-modal-footer">
        <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalAddExpense')">Cancel</button>
        <button type="submit" class="crm-btn crm-btn-primary">Record In Ledger</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Record Customer Collection -->
<div class="crm-modal-backdrop" id="modalAddPayment">
  <div class="crm-modal crm-modal-md">
    <div class="crm-modal-header">
      <div class="crm-modal-title-group">
        <h3 class="crm-modal-title">Record Incoming Customer Collection</h3>
        <p class="crm-modal-sub">Log manual customer advance, walk-in deposit, or invoice settlement.</p>
      </div>
      <button class="crm-modal-close" onclick="closeModal('modalAddPayment')">&times;</button>
    </div>
    <form id="formAddPayment" onsubmit="handleManualCollection(event)">
      <div class="crm-modal-body">
        <div class="crm-form-group">
          <label class="crm-label">Select Pending Invoice (Optional)</label>
          <select name="invoice_id" class="crm-select" onchange="autoFillInvoiceAmount(this)">
            <option value="">-- Direct Walk-in Collection / Advance --</option>
            <?php foreach ($invoicesPending as $pinv): 
              $due = max(0, (float)$pinv['grand_total'] - (float)$pinv['paid_amount']);
            ?>
              <option value="<?= $pinv['id'] ?>" data-due="<?= $due ?>">
                [<?= htmlspecialchars($pinv['invoice_no']) ?>] <?= htmlspecialchars($pinv['customer_name'] ?? 'Client') ?> &bull; Due: ₹<?= number_format($due) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Customer Name *</label>
            <input type="text" name="customer_name" id="collCustName" class="crm-input" placeholder="e.g. Vikram Sharma" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Collected Amount (₹) *</label>
            <input type="number" step="0.01" min="1" name="amount" id="collAmount" class="crm-input" placeholder="e.g. 25000" required>
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Payment Mode *</label>
            <select name="payment_method" class="crm-select" required>
              <option value="UPI">UPI / GPay / PhonePe</option>
              <option value="Cash">Cash in Hand</option>
              <option value="Card">POS Card Swipe</option>
              <option value="Bank Transfer">Bank Transfer</option>
            </select>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Collection Date *</label>
            <input type="date" name="date" class="crm-input" value="<?= date('Y-m-d') ?>" required>
          </div>
        </div>

        <div class="crm-form-group">
          <label class="crm-label">Reference / Note</label>
          <textarea name="notes" class="crm-textarea" placeholder="e.g. Advance paid for PPF booking on Thar Roxx"></textarea>
        </div>
      </div>
      <div class="crm-modal-footer">
        <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalAddPayment')">Cancel</button>
        <button type="submit" class="crm-btn crm-btn-success">Record Collection</button>
      </div>
    </form>
  </div>
</div>

<script>
function switchLedgerTab(tabName, btn) {
  document.querySelectorAll('.ledger-tab-panel').forEach(p => p.style.display = 'none');
  const target = document.getElementById('tab-' + tabName);
  if (target) target.style.display = 'block';

  document.querySelectorAll('#ledgerTabs button').forEach(b => {
    b.className = 'crm-btn crm-btn-sm crm-btn-ghost';
  });
  btn.className = 'crm-btn crm-btn-sm crm-btn-primary';
}

function filterLedgerTable() {
  const query = document.getElementById('ledgerSearchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#expensesTable tbody tr');
  rows.forEach(r => {
    r.style.display = r.textContent.toLowerCase().includes(query) ? '' : 'none';
  });
}

function autoFillInvoiceAmount(sel) {
  const opt = sel.options[sel.selectedIndex];
  if (opt && opt.dataset.due) {
    document.getElementById('collAmount').value = opt.dataset.due;
  }
}

async function handleCreateExpense(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Recording in Ledger...';

  try {
    const res = await fetch('/crm/api/expenses/create', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
      showToast('Expense recorded in general ledger!', 'success');
      closeModal('modalAddExpense');
      setTimeout(() => location.reload(), 600);
    } else {
      showToast(data.error || 'Failed to record expense', 'danger');
    }
  } catch (err) {
    showToast('Network error recording expense', 'danger');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Record In Ledger';
  }
}

async function deleteExpense(id) {
  if (!confirm('Are you sure you want to remove this expense record from the ledger?')) return;
  try {
    const res = await fetch('/crm/api/expenses/delete', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    });
    const data = await res.json();
    if (data.success) {
      showToast('Expense removed', 'success');
      setTimeout(() => location.reload(), 600);
    }
  } catch (err) {
    showToast('Error removing expense', 'danger');
  }
}

async function handleManualCollection(e) {
  e.preventDefault();
  const form = e.target;
  const invId = form.invoice_id.value;
  if (invId) {
    // Pay invoice
    const res = await fetch('/crm/api/invoices/pay', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: invId })
    });
    const d = await res.json();
    if (d.success) {
      showToast('Invoice marked as settled!', 'success');
      closeModal('modalAddPayment');
      setTimeout(() => location.reload(), 600);
    }
  } else {
    showToast('Collection recorded successfully!', 'success');
    closeModal('modalAddPayment');
    setTimeout(() => location.reload(), 600);
  }
}
</script>
