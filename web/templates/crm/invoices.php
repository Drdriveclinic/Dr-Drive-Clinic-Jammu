<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Invoices & Billing
$invoices = Database::query("
  SELECT i.*, 
         COALESCE(i.customer_name, c.name, 'Walk-in Client') as cust_display_name,
         COALESCE(i.customer_phone, c.phone, '—') as cust_display_phone,
         COALESCE(i.vehicle_model, v.model, 'Vehicle') as veh_display_model,
         COALESCE(i.vehicle_reg, v.registration, 'Pending Reg') as veh_display_reg
  FROM invoices i
  LEFT JOIN customers c ON i.customer_id = c.id
  LEFT JOIN vehicles v ON i.vehicle_id = v.id
  ORDER BY i.created_at DESC
");

$customers = Database::query("SELECT id, name, phone, email FROM customers ORDER BY name ASC");
$storeProducts = Database::query("SELECT id, sku, name, sell_price, unit_cost, unit, current_stock FROM inventory_items ORDER BY name ASC");
$servicesList = Database::query("SELECT id, slug, name, category FROM services WHERE is_active = 1 ORDER BY sort_order ASC");

// Totals
$totalInvoiced = 0.0;
$totalCollected = 0.0;
$totalPending = 0.0;
$totalGst = 0.0;

foreach ($invoices as $inv) {
    $grand = (float)$inv['grand_total'];
    $paid = (float)($inv['paid_amount'] ?: ($inv['payment_status'] === 'PAID' ? $grand : 0));
    $totalInvoiced += $grand;
    $totalCollected += $paid;
    $totalPending += max(0, $grand - $paid);
    $totalGst += (float)$inv['gst_amount'];
}
?>
<div class="crm-invoices-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">BUSINESS &bull; BILLING & GST</div>
      <h1 class="crm-page-heading">Quotations & Tax Invoices</h1>
      <p class="crm-page-sub">GST-compliant automotive invoices, custom services & product pricing, PDF printing, and instant 1-click WhatsApp delivery.</p>
    </div>
    <div class="crm-page-actions">
      <button class="crm-btn crm-btn-primary" onclick="openCreateInvoiceModal()">+ Create Tax Invoice</button>
    </div>
  </div>

  <!-- Financial Metrics -->
  <div class="crm-kpi-grid">
    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Total Invoiced Gross</span>
        <span class="crm-kpi-icon" style="color:#38bdf8;">🧾</span>
      </div>
      <div class="crm-kpi-value">₹<?= number_format($totalInvoiced) ?></div>
      <div class="crm-kpi-change positive"><?= count($invoices) ?> Total Invoices Issued</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Collected Revenue</span>
        <span class="crm-kpi-icon" style="color:#10b981;">💰</span>
      </div>
      <div class="crm-kpi-value" style="color:#10b981;">₹<?= number_format($totalCollected) ?></div>
      <div class="crm-kpi-change positive">Settled via UPI, Cash & Card</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Outstanding Receivables</span>
        <span class="crm-kpi-icon" style="color:#f59e0b;">⏳</span>
      </div>
      <div class="crm-kpi-value" style="color:#f59e0b;">₹<?= number_format($totalPending) ?></div>
      <div class="crm-kpi-change neutral">Pending Client Settlement</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">GST Tax Liability (18%)</span>
        <span class="crm-kpi-icon" style="color:#a855f7;">🏛️</span>
      </div>
      <div class="crm-kpi-value" style="color:#c084fc;">₹<?= number_format($totalGst) ?></div>
      <div class="crm-kpi-change neutral">CGST (9%) + SGST (9%)</div>
    </div>
  </div>

  <!-- Invoices Table Card -->
  <div class="crm-card" style="margin-top:24px;">
    <div class="crm-card-header" style="border-bottom:1px solid var(--crm-border); padding:16px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <h2 class="crm-card-title">All Studio Invoices & Receipts</h2>
      <div style="display:flex; gap:10px;">
        <select id="invoiceStatusFilter" class="crm-select" style="width:140px; padding:6px 10px; font-size:12px;" onchange="filterInvoices()">
          <option value="">All Statuses</option>
          <option value="PAID">Paid</option>
          <option value="PENDING">Pending</option>
        </select>
        <input type="text" id="invoiceSearchInput" placeholder="Search invoice, client, or car..." class="crm-input" style="width:220px; padding:6px 12px; font-size:12px;" oninput="filterInvoices()">
      </div>
    </div>

    <div class="crm-table-container">
      <table class="crm-table" id="invoicesTable">
        <thead>
          <tr>
            <th>Invoice #</th>
            <th>Billing Date</th>
            <th>Customer & Vehicle</th>
            <th>Billed Services / Items</th>
            <th>Subtotal</th>
            <th>GST (18%)</th>
            <th>Grand Total</th>
            <th>Payment Status</th>
            <th>Payment Mode</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($invoices)): ?>
            <?php foreach ($invoices as $inv): 
              $cleanP = preg_replace('/\D/', '', (string)$inv['cust_display_phone']);
              if (strlen($cleanP) === 10) $cleanP = '91' . $cleanP;
              $siteUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
              $invUrl = "{$siteUrl}/crm/invoices/{$inv['id']}/print";
              $waMsg = urlencode(
                  "⚡ *THE DRIVE CLINIC — INVOICE NOTICE* ⚡\n\n" .
                  "Dear {$inv['cust_display_name']},\n" .
                  "Your vehicle {$inv['veh_display_model']} ({$inv['veh_display_reg']}) invoice #{$inv['invoice_no']} for ₹" . number_format((float)$inv['grand_total']) . " is available.\n\n" .
                  "📄 View & Print Invoice: {$invUrl}\n\n" .
                  "Thank you for trusting The Drive Clinic Jammu!"
              );

              // Summary of items
              $itemsSummary = 'Automotive Detailing';
              if (!empty($inv['items_json'])) {
                  $parsedItems = json_decode($inv['items_json'], true);
                  if (is_array($parsedItems) && !empty($parsedItems)) {
                      $names = array_map(fn($it) => $it['name'] ?? '', $parsedItems);
                      $itemsSummary = implode(', ', array_filter($names));
                  }
              } elseif (!empty($inv['notes'])) {
                  $itemsSummary = $inv['notes'];
              }
            ?>
            <tr data-status="<?= htmlspecialchars($inv['payment_status']) ?>">
              <td><strong class="crm-ref-code"><?= htmlspecialchars($inv['invoice_no']) ?></strong></td>
              <td><?= date('d M Y', strtotime($inv['created_at'])) ?></td>
              <td>
                <strong><?= htmlspecialchars($inv['cust_display_name']) ?></strong>
                <div class="crm-text-muted" style="font-size:11px;">
                  <?= htmlspecialchars($inv['veh_display_model']) ?>
                  <span class="crm-plate-tag"><?= htmlspecialchars($inv['veh_display_reg']) ?></span>
                </div>
              </td>
              <td>
                <div class="crm-service-tag" style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= htmlspecialchars($itemsSummary) ?>">
                  <?= htmlspecialchars($itemsSummary) ?>
                </div>
              </td>
              <td>₹<?= number_format((float)$inv['subtotal']) ?></td>
              <td><?= (float)$inv['gst_amount'] > 0 ? '₹' . number_format((float)$inv['gst_amount']) : '—' ?></td>
              <td><strong class="crm-amount-val" style="font-size:15px; color:#fff;">₹<?= number_format((float)$inv['grand_total']) ?></strong></td>
              <td>
                <?php if ($inv['payment_status'] === 'PAID'): ?>
                  <span class="crm-pill crm-pill-success">Paid</span>
                <?php else: ?>
                  <span class="crm-pill crm-pill-warning">Pending (₹<?= number_format((float)$inv['grand_total'] - (float)$inv['paid_amount']) ?>)</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="crm-source-badge"><?= htmlspecialchars($inv['payment_method'] ?: 'Pending') ?></span>
              </td>
              <td style="text-align:right;">
                <div class="crm-action-group" style="justify-content:flex-end; gap:6px;">
                  <?php if ($inv['payment_status'] !== 'PAID'): ?>
                    <button class="crm-btn crm-btn-xs crm-btn-success" onclick="markInvoicePaid(<?= $inv['id'] ?>)">
                      ✓ Pay
                    </button>
                  <?php endif; ?>
                  <a href="/crm/invoices/<?= $inv['id'] ?>/print" target="_blank" class="crm-btn crm-btn-xs crm-btn-ghost" title="Print or Download PDF">
                    🖨️ Print
                  </a>
                  <a href="https://wa.me/<?= $cleanP ?>?text=<?= $waMsg ?>" target="_blank" rel="noopener" class="crm-btn crm-btn-xs" style="background:#25D366; color:#000; font-weight:700;" title="Share on WhatsApp">
                    💬 WhatsApp
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="10" style="text-align:center; padding:44px 20px; color:#888888;">
                <div style="font-size:32px; margin-bottom:10px;">🧾</div>
                <strong style="color:#ffffff; font-size:14px;">No Invoices Generated Yet</strong>
                <p style="font-size:12px; margin:6px 0 0; color:#666666;">Click "+ Create Tax Invoice" above to generate a custom invoice.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal: Create New Tax Invoice with Custom Line Items & Pricing -->
<div class="crm-modal-backdrop" id="modalCreateInvoice">
  <div class="crm-modal crm-modal-lg" style="max-width:860px;">
    <div class="crm-modal-header">
      <div class="crm-modal-title-group">
        <h3 class="crm-modal-title">Create GST Tax Invoice / Billing</h3>
        <p class="crm-modal-sub">Add custom service names, custom product pricing, client specs, and GST calculation.</p>
      </div>
      <button class="crm-modal-close" onclick="closeModal('modalCreateInvoice')">&times;</button>
    </div>
    <form id="formCreateInvoice" onsubmit="handleCreateInvoiceSubmit(event)">
      <div class="crm-modal-body">
        
        <!-- Client & Vehicle Selection -->
        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:8px; padding:18px; margin-bottom:20px;">
          <div style="font-size:11px; font-weight:800; color:var(--red); letter-spacing:0.12em; text-transform:uppercase; margin-bottom:12px;">CLIENT & VEHICLE DETAILS</div>
          
          <div class="crm-form-row">
            <div class="crm-form-group">
              <label class="crm-label">Customer Name *</label>
              <input type="text" name="customer_name" id="invCustName" class="crm-input" placeholder="e.g. Vikram Sharma" required>
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Mobile Phone Number *</label>
              <input type="tel" name="customer_phone" id="invCustPhone" class="crm-input" placeholder="+91 94191 XXXXX" required>
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Email Address</label>
              <input type="email" name="customer_email" id="invCustEmail" class="crm-input" placeholder="client@example.com">
            </div>
          </div>

          <div class="crm-form-row">
            <div class="crm-form-group">
              <label class="crm-label">Vehicle Make & Model *</label>
              <input type="text" name="vehicle_model" id="invVehModel" class="crm-input" placeholder="e.g. BMW 320d M-Sport" required>
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Registration Number</label>
              <input type="text" name="vehicle_reg" id="invVehReg" class="crm-input" placeholder="e.g. JK 02 CQ 7788" style="text-transform:uppercase;">
            </div>
          </div>
        </div>

        <!-- Dynamic Custom Services & Products Table -->
        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:8px; padding:18px; margin-bottom:20px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:8px;">
            <div style="font-size:11px; font-weight:800; color:var(--red); letter-spacing:0.12em; text-transform:uppercase;">
              CUSTOM SERVICES & PRODUCTS LINE ITEMS
            </div>
            <div style="display:flex; gap:8px;">
              <button type="button" class="crm-btn crm-btn-xs crm-btn-secondary" onclick="addCustomLineItem('service')">+ Add Custom Service</button>
              <button type="button" class="crm-btn crm-btn-xs crm-btn-ghost" onclick="addCustomLineItem('product')">+ Add Custom Product</button>
            </div>
          </div>

          <!-- Quick Service / Store Template Inserter -->
          <div style="display:flex; gap:10px; margin-bottom:14px; background:#0F1420; padding:10px 14px; border-radius:6px; align-items:center;">
            <span style="font-size:11px; color:#94A3B8; font-weight:700;">Quick Insert:</span>
            <select id="quickServiceSelect" class="crm-select" style="font-size:12px; padding:4px 8px; flex:1;" onchange="insertPresetService(this)">
              <option value="">-- Choose Studio Package --</option>
              <option value="STEK DYNOshield Gloss Full PPF|service|75000">STEK DYNOshield Gloss Full PPF (₹75,000)</option>
              <option value="Gtechniq 9H Crystal Serum Ultra Ceramic|service|35000">Gtechniq 9H Crystal Serum Ultra Ceramic (₹35,000)</option>
              <option value="Garware Premium Plus Frontal PPF|service|28000">Garware Premium Plus Frontal PPF (₹28,000)</option>
              <option value="Bumper-to-Bumper Full Car Detailing|service|12000">Bumper-to-Bumper Full Car Detailing (₹12,000)</option>
              <option value="Superbike Full Precision Detailing & Ceramic|service|8500">Superbike Full Precision Detailing & Ceramic (₹8,500)</option>
              <option value="Signature Ceramic SiO2 Foam Wash|service|2500">Signature Ceramic SiO2 Foam Wash (₹2,500)</option>
            </select>
          </div>

          <!-- Table of items -->
          <table style="width:100%; border-collapse:collapse; font-size:12px;" id="invItemsTable">
            <thead>
              <tr style="border-bottom:1px solid rgba(255,255,255,0.12); color:#94A3B8; text-transform:uppercase; font-size:10px;">
                <th style="padding:8px; text-align:left;">Service / Product Name (Custom)</th>
                <th style="padding:8px; width:100px; text-align:center;">Type</th>
                <th style="padding:8px; width:70px; text-align:center;">Qty</th>
                <th style="padding:8px; width:120px; text-align:right;">Custom Rate (₹)</th>
                <th style="padding:8px; width:120px; text-align:right;">Total (₹)</th>
                <th style="padding:8px; width:40px; text-align:center;"></th>
              </tr>
            </thead>
            <tbody id="invItemsBody">
              <!-- Default Initial Row -->
              <tr>
                <td style="padding:8px;">
                  <input type="text" class="crm-input item-name" value="Paint Protection Film (PPF) — STEK DYNOshield" placeholder="Enter custom service or product name" required style="font-size:12px; padding:6px 10px;">
                </td>
                <td style="padding:8px; text-align:center;">
                  <select class="crm-select item-type" style="font-size:11px; padding:4px 6px;">
                    <option value="service" selected>Service</option>
                    <option value="product">Product</option>
                  </select>
                </td>
                <td style="padding:8px; text-align:center;">
                  <input type="number" min="1" step="1" class="crm-input item-qty" value="1" style="font-size:12px; padding:6px; text-align:center;" oninput="recalcInvoiceTotals()">
                </td>
                <td style="padding:8px; text-align:right;">
                  <input type="number" min="0" step="100" class="crm-input item-rate" value="75000" style="font-size:12px; padding:6px 10px; text-align:right;" oninput="recalcInvoiceTotals()">
                </td>
                <td style="padding:8px; text-align:right; font-weight:700; color:#fff;" class="item-total">₹75,000</td>
                <td style="padding:8px; text-align:center;">
                  <button type="button" class="crm-btn crm-btn-xs crm-btn-danger" onclick="removeLineItem(this)">✕</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Calculation & Settlement Details -->
        <div class="crm-form-row" style="align-items:flex-start;">
          <div class="crm-form-group" style="flex:1;">
            <label class="crm-label">Invoice Notes / Terms</label>
            <textarea name="notes" class="crm-textarea" rows="3" placeholder="e.g. Includes 5-year digital warranty, complimentary 6-month inspection, and 1 maintenance wash."></textarea>
          </div>

          <div style="width:340px; background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:16px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:13px;">
              <span style="color:#94A3B8;">Subtotal:</span>
              <strong style="color:#fff;" id="invCalcSubtotal">₹75,000</strong>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; font-size:13px;">
              <span style="color:#94A3B8;">Privilege Discount (₹):</span>
              <input type="number" min="0" step="100" id="invDiscountInput" value="0" class="crm-input" style="width:110px; font-size:12px; padding:4px 8px; text-align:right;" oninput="recalcInvoiceTotals()">
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; font-size:13px;">
              <label style="display:flex; align-items:center; gap:6px; cursor:pointer; color:#94A3B8;">
                <input type="checkbox" id="invGstToggle" checked onchange="recalcInvoiceTotals()" style="accent-color:var(--red);">
                <span>18% GST (9% CGST + 9% SGST)</span>
              </label>
              <strong style="color:#fff;" id="invCalcGst">₹13,500</strong>
            </div>

            <div style="border-top:1px solid rgba(255,255,255,0.2); padding-top:10px; margin-top:10px; display:flex; justify-content:space-between; font-size:18px; font-weight:800;">
              <span style="color:#fff;">Grand Total:</span>
              <span style="color:var(--red);" id="invCalcGrand">₹88,500</span>
            </div>
          </div>
        </div>

        <!-- Payment Settlement -->
        <div class="crm-form-row" style="margin-top:16px;">
          <div class="crm-form-group">
            <label class="crm-label">Payment Status *</label>
            <select name="payment_status" id="invPayStatus" class="crm-select" onchange="onPaymentStatusChange(this)">
              <option value="PAID">Paid in Full</option>
              <option value="PENDING">Pending / Due</option>
            </select>
          </div>

          <div class="crm-form-group">
            <label class="crm-label">Payment Mode *</label>
            <select name="payment_method" class="crm-select">
              <option value="UPI">UPI (GPay / PhonePe / Paytm)</option>
              <option value="Cash">Cash in Hand</option>
              <option value="Card">POS Card Swipe</option>
              <option value="Bank Transfer">Bank NEFT / RTGS</option>
            </select>
          </div>

          <div class="crm-form-group">
            <label class="crm-label">Paid Amount (₹)</label>
            <input type="number" step="0.01" name="paid_amount" id="invPaidAmount" class="crm-input" value="88500">
          </div>
        </div>

      </div>
      <div class="crm-modal-footer">
        <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalCreateInvoice')">Cancel</button>
        <button type="submit" class="crm-btn crm-btn-primary">Generate Tax Invoice</button>
      </div>
    </form>
  </div>
</div>

<script>
function openCreateInvoiceModal() {
  openModal('modalCreateInvoice');
  recalcInvoiceTotals();
}

function addCustomLineItem(type = 'service', name = '', rate = 0) {
  const tbody = document.getElementById('invItemsBody');
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td style="padding:8px;">
      <input type="text" class="crm-input item-name" value="${name}" placeholder="${type === 'service' ? 'e.g. Matte PPF Roof Wrap' : 'e.g. Gtechniq C2 Liquid Crystal (500ml)'}" required style="font-size:12px; padding:6px 10px;">
    </td>
    <td style="padding:8px; text-align:center;">
      <select class="crm-select item-type" style="font-size:11px; padding:4px 6px;">
        <option value="service" ${type === 'service' ? 'selected' : ''}>Service</option>
        <option value="product" ${type === 'product' ? 'selected' : ''}>Product</option>
      </select>
    </td>
    <td style="padding:8px; text-align:center;">
      <input type="number" min="1" step="1" class="crm-input item-qty" value="1" style="font-size:12px; padding:6px; text-align:center;" oninput="recalcInvoiceTotals()">
    </td>
    <td style="padding:8px; text-align:right;">
      <input type="number" min="0" step="100" class="crm-input item-rate" value="${rate}" style="font-size:12px; padding:6px 10px; text-align:right;" oninput="recalcInvoiceTotals()">
    </td>
    <td style="padding:8px; text-align:right; font-weight:700; color:#fff;" class="item-total">₹${rate.toLocaleString()}</td>
    <td style="padding:8px; text-align:center;">
      <button type="button" class="crm-btn crm-btn-xs crm-btn-danger" onclick="removeLineItem(this)">✕</button>
    </td>
  `;
  tbody.appendChild(tr);
  recalcInvoiceTotals();
}

function removeLineItem(btn) {
  const tbody = document.getElementById('invItemsBody');
  if (tbody.querySelectorAll('tr').length > 1) {
    btn.closest('tr').remove();
    recalcInvoiceTotals();
  } else {
    showToast('Invoice must have at least one line item', 'warning');
  }
}

function insertPresetService(sel) {
  if (!sel.value) return;
  const [name, type, rate] = sel.value.split('|');
  addCustomLineItem(type, name, parseFloat(rate));
  sel.value = '';
}

function recalcInvoiceTotals() {
  const rows = document.querySelectorAll('#invItemsBody tr');
  let subtotal = 0;

  rows.forEach(r => {
    const qty = parseFloat(r.querySelector('.item-qty').value || 1);
    const rate = parseFloat(r.querySelector('.item-rate').value || 0);
    const rowTotal = qty * rate;
    r.querySelector('.item-total').textContent = '₹' + rowTotal.toLocaleString();
    subtotal += rowTotal;
  });

  const discount = parseFloat(document.getElementById('invDiscountInput').value || 0);
  const netSubtotal = Math.max(0, subtotal - discount);
  const hasGst = document.getElementById('invGstToggle').checked;
  const gst = hasGst ? Math.round(netSubtotal * 0.18) : 0;
  const grand = netSubtotal + gst;

  document.getElementById('invCalcSubtotal').textContent = '₹' + subtotal.toLocaleString();
  document.getElementById('invCalcGst').textContent = '₹' + gst.toLocaleString();
  document.getElementById('invCalcGrand').textContent = '₹' + grand.toLocaleString();

  if (document.getElementById('invPayStatus').value === 'PAID') {
    document.getElementById('invPaidAmount').value = grand;
  }
}

function onPaymentStatusChange(sel) {
  if (sel.value === 'PENDING') {
    document.getElementById('invPaidAmount').value = 0;
  } else {
    recalcInvoiceTotals();
  }
}

function filterInvoices() {
  const status = document.getElementById('invoiceStatusFilter').value.toUpperCase();
  const query = document.getElementById('invoiceSearchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#invoicesTable tbody tr');

  rows.forEach(r => {
    const rowStatus = r.dataset.status || '';
    const text = r.textContent.toLowerCase();
    const matchStatus = !status || rowStatus === status;
    const matchQuery = !query || text.includes(query);
    r.style.display = matchStatus && matchQuery ? '' : 'none';
  });
}

async function handleCreateInvoiceSubmit(e) {
  e.preventDefault();
  const form = e.target;
  const rows = document.querySelectorAll('#invItemsBody tr');
  const items = [];

  rows.forEach(r => {
    items.push({
      name: r.querySelector('.item-name').value.trim(),
      type: r.querySelector('.item-type').value,
      qty: parseFloat(r.querySelector('.item-qty').value || 1),
      rate: parseFloat(r.querySelector('.item-rate').value || 0),
      total: parseFloat(r.querySelector('.item-qty').value || 1) * parseFloat(r.querySelector('.item-rate').value || 0)
    });
  });

  const discount = parseFloat(document.getElementById('invDiscountInput').value || 0);
  const hasGst = document.getElementById('invGstToggle').checked ? 1 : 0;

  const payload = {
    customer_name: form.customer_name.value.trim(),
    customer_phone: form.customer_phone.value.trim(),
    customer_email: form.customer_email.value.trim(),
    vehicle_model: form.vehicle_model.value.trim(),
    vehicle_reg: form.vehicle_reg.value.trim(),
    items: items,
    discount: discount,
    has_gst: hasGst,
    payment_status: form.payment_status.value,
    payment_method: form.payment_method.value,
    paid_amount: parseFloat(form.paid_amount.value || 0),
    notes: form.notes.value.trim()
  };

  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Generating Tax Invoice...';

  try {
    const res = await fetch('/crm/api/invoices/create', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (data.success) {
      showToast('Invoice #' + (data.invoice_no || '') + ' generated successfully!', 'success');
      closeModal('modalCreateInvoice');
      setTimeout(() => location.reload(), 600);
    } else {
      showToast(data.error || 'Failed to generate invoice', 'danger');
    }
  } catch (err) {
    showToast('Network error creating invoice', 'danger');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Generate Tax Invoice';
  }
}

async function markInvoicePaid(id) {
  if (!confirm('Mark this invoice as PAID in full?')) return;
  try {
    const res = await fetch('/crm/api/invoices/pay', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    });
    const data = await res.json();
    if (data.success) {
      showToast('Invoice marked as PAID!', 'success');
      setTimeout(() => location.reload(), 600);
    }
  } catch (err) {
    showToast('Error updating payment', 'danger');
  }
}
</script>
