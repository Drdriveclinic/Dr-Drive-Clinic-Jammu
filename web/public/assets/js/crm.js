/**
 * THE DRIVE CLINIC — Studio CRM Client-Side Controller
 * Handles live inventory updates, decline & wastage logging, modal interactions, search filters, and AJAX requests.
 */

// ==========================================
// 1. Toast Notifications
// ==========================================
function showToast(message, type = 'success') {
  const container = document.getElementById('crmToastContainer');
  if (!container) return;

  const toast = document.createElement('div');
  toast.className = `crm-toast crm-toast-${type}`;
  
  const icon = type === 'success' ? '✅' : (type === 'danger' ? '⚠️' : 'ℹ️');
  toast.innerHTML = `
    <span class="crm-toast-icon">${icon}</span>
    <span class="crm-toast-msg">${message}</span>
    <button class="crm-toast-close" onclick="this.parentElement.remove()">&times;</button>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.classList.add('crm-toast-fade');
    setTimeout(() => toast.remove(), 400);
  }, 4000);
}

// ==========================================
// 2. Modal Controls
// ==========================================
function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
}

// Close on backdrop click
document.addEventListener('click', function(e) {
  if (e.target.classList.contains('crm-modal-backdrop')) {
    e.target.classList.remove('active');
    document.body.style.overflow = '';
  }
});

// Close on ESC key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.crm-modal-backdrop.active').forEach(m => {
      m.classList.remove('active');
    });
    document.body.style.overflow = '';
  }
});

function openNewBookingModal() {
  openModal('modalNewBooking');
}

function openAddProductModal() {
  openModal('modalAddProduct');
}

function openStockInModal(itemId = null) {
  const modal = document.getElementById('modalStockIn');
  if (modal) {
    if (itemId) {
      const select = modal.querySelector('select[name="item_id"]');
      if (select) select.value = itemId;
    }
    openModal('modalStockIn');
  }
}

function openDeclineStockModal(itemId = null) {
  const modal = document.getElementById('modalDeclineStock');
  if (modal) {
    if (itemId) {
      const select = modal.querySelector('select[name="item_id"]');
      if (select) {
        select.value = itemId;
        updateDeclineProductDetails(select);
      }
    }
    openModal('modalDeclineStock');
  }
}

// ==========================================
// 3. Dynamic Decline Loss Calculator
// ==========================================
let currentSelectedProduct = null;

function updateDeclineProductDetails(selectEl) {
  const opt = selectEl.options[selectEl.selectedIndex];
  const unitLabel = document.getElementById('declineUnitLabel');
  const lossCard = document.getElementById('declineLossCard');
  const qtyInput = document.getElementById('declineQtyInput');

  if (!opt || !opt.value) {
    currentSelectedProduct = null;
    if (unitLabel) unitLabel.textContent = 'Units';
    if (lossCard) lossCard.style.display = 'none';
    return;
  }

  currentSelectedProduct = {
    id: opt.value,
    unit: opt.dataset.unit || 'Units',
    stock: parseFloat(opt.dataset.stock || 0),
    cost: parseFloat(opt.dataset.cost || 0)
  };

  if (unitLabel) unitLabel.textContent = currentSelectedProduct.unit;
  if (lossCard) lossCard.style.display = 'block';
  if (qtyInput) qtyInput.max = currentSelectedProduct.stock;

  calculateDeclineLoss();
}

function calculateDeclineLoss() {
  if (!currentSelectedProduct) return;
  const qtyInput = document.getElementById('declineQtyInput');
  const lossValEl = document.getElementById('declineLossVal');
  const remainEl = document.getElementById('declineStockRemaining');

  const qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
  const totalLoss = qty * currentSelectedProduct.cost;
  const remaining = Math.max(0, currentSelectedProduct.stock - qty);

  if (lossValEl) {
    lossValEl.textContent = '₹' + totalLoss.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  if (remainEl) {
    remainEl.innerHTML = `Current in store: <strong>${currentSelectedProduct.stock} ${currentSelectedProduct.unit}</strong> &rarr; After decline: <strong style="color: ${remaining === 0 ? '#ef4444' : '#10b981'}">${remaining.toFixed(1)} ${currentSelectedProduct.unit}</strong>`;
  }
}

// ==========================================
// 4. AJAX Form Submissions
// ==========================================

// Add Product to Store
async function handleAddProduct(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Adding to Catalog...';

  try {
    const res = await fetch('/crm/api/products', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    if (data.success) {
      showToast(data.message || 'Product registered in store successfully!', 'success');
      closeModal('modalAddProduct');
      form.reset();
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(data.error || 'Failed to add product', 'danger');
    }
  } catch (err) {
    showToast('Network error adding product', 'danger');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Add to Store Inventory';
  }
}

// Stock In / Purchase
async function handleStockIn(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Updating Stock...';

  try {
    const res = await fetch('/crm/api/stock-in', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    if (data.success) {
      showToast(data.message || 'Stock-in recorded successfully!', 'success');
      closeModal('modalStockIn');
      form.reset();
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(data.error || 'Failed to update stock', 'danger');
    }
  } catch (err) {
    showToast('Network error recording stock-in', 'danger');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Add Received Stock';
  }
}

// Decline / Scrap Logger (KEY USER FEATURE)
async function handleDeclineStock(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Logging Decline & Writing Off...';

  try {
    const res = await fetch('/crm/api/decline', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    if (data.success) {
      showToast(data.message || 'Decline and financial loss recorded successfully!', 'danger');
      closeModal('modalDeclineStock');
      form.reset();
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(data.error || 'Failed to log decline', 'danger');
    }
  } catch (err) {
    showToast('Network error recording decline', 'danger');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Confirm Decline & Write-Off';
  }
}

// Create New Booking
async function handleCreateBooking(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Scheduling...';

  try {
    const res = await fetch('/crm/api/bookings', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    if (data.success) {
      showToast(data.message || 'Booking scheduled successfully!', 'success');
      closeModal('modalNewBooking');
      form.reset();
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(data.error || 'Failed to schedule booking', 'danger');
    }
  } catch (err) {
    showToast('Network error scheduling booking', 'danger');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Confirm & Dispatch';
  }
}

// Quick Check-in to Workshop Bay
function quickCheckIn(customerName, carModel, serviceName) {
  const bay = prompt(`Assign Bay for ${customerName} (${carModel}) [Enter 1, 2, 3, or 4]:`, '1');
  if (bay) {
    showToast(`Vehicle ${carModel} checked into Bay 0${bay}! Job Card generated.`, 'success');
    setTimeout(() => {
      window.location.href = '/crm/job-cards';
    }, 1000);
  }
}

// Mark Invoice Paid
async function markInvoicePaid(invId) {
  if (!confirm('Mark invoice as fully PAID?')) return;
  try {
    const res = await fetch('/crm/api/invoices/pay', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: invId })
    });
    const data = await res.json();
    if (data.success) {
      showToast('Invoice marked as PAID!', 'success');
      setTimeout(() => location.reload(), 800);
    }
  } catch (err) {
    showToast('Error marking invoice paid', 'danger');
  }
}

// ==========================================
// 5. Inventory Tab Switcher & Filters
// ==========================================
function switchInventoryTab(tabName, btnEl) {
  document.querySelectorAll('.crm-tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.crm-tab-pane').forEach(p => p.classList.remove('active'));

  if (btnEl) btnEl.classList.add('active');

  if (tabName === 'catalog') {
    document.getElementById('paneCatalog')?.classList.add('active');
  } else if (tabName === 'declines') {
    document.getElementById('paneDeclines')?.classList.add('active');
  } else if (tabName === 'ledger') {
    document.getElementById('paneLedger')?.classList.add('active');
  }
}

// Live Search & Category Filtering
function filterInventoryCatalog() {
  const searchInput = document.getElementById('inventorySearch');
  const catFilter = document.getElementById('categoryFilter');
  if (!searchInput) return;

  const query = searchInput.value.toLowerCase().trim();
  const selectedCat = catFilter ? catFilter.value.toUpperCase() : '';

  const rows = document.querySelectorAll('.crm-inv-row');
  rows.forEach(row => {
    const sku = (row.dataset.sku || '').toLowerCase();
    const name = (row.dataset.name || '').toLowerCase();
    const supplier = (row.dataset.supplier || '').toLowerCase();
    const cat = (row.dataset.cat || '').toUpperCase();

    const matchesQuery = !query || sku.includes(query) || name.includes(query) || supplier.includes(query);
    const matchesCat = !selectedCat || cat === selectedCat;

    row.style.display = (matchesQuery && matchesCat) ? '' : 'none';
  });
}

// Hash-based tab activation (e.g. /crm/inventory#declines)
window.addEventListener('DOMContentLoaded', () => {
  if (window.location.hash === '#declines') {
    const btn = document.getElementById('tabDeclinesBtn');
    if (btn) switchInventoryTab('declines', btn);
  }

  const sidebarToggle = document.getElementById('crmSidebarToggle');
  const sidebar = document.getElementById('crmSidebar');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      if (sidebar.style.display === 'flex') {
        sidebar.style.display = 'none';
      } else {
        sidebar.style.display = 'flex';
        sidebar.style.position = 'fixed';
        sidebar.style.zIndex = '999';
      }
    });
  }
});

// ==========================================
// 6. LEADS PIPELINE & QUOTE BUILDER CONTROLLER
// ==========================================

function openAddLeadModal() {
  openModal('modalAddLead');
}

// Filter leads on search input
function filterPipelineLeads() {
  const input = document.getElementById('pipelineSearch');
  if (!input) return;
  const q = input.value.toLowerCase().trim();
  const cards = document.querySelectorAll('.crm-lead-card');

  cards.forEach(c => {
    const text = (c.dataset.search || '').toLowerCase();
    if (!q || text.includes(q)) {
      c.style.display = '';
    } else {
      c.style.display = 'none';
    }
  });
}

// Create New Lead
async function handleCreateLead(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Adding to Pipeline...';

  try {
    const res = await fetch('/crm/api/leads/create', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    if (data.success) {
      showToast(data.message || 'Lead added to pipeline successfully!', 'success');
      closeModal('modalAddLead');
      form.reset();
      setTimeout(() => location.reload(), 700);
    } else {
      showToast(data.error || 'Failed to create lead', 'danger');
    }
  } catch (err) {
    showToast('Network error creating lead', 'danger');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Add to Pipeline';
  }
}

// Move lead stage (e.g. Mark Contacted)
async function updateLeadStage(leadId, newStage) {
  try {
    const res = await fetch('/crm/api/leads/status', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ lead_id: leadId, stage: newStage })
    });
    const data = await res.json();
    if (data.success) {
      showToast(data.message || `Lead moved to ${newStage}!`, 'success');
      setTimeout(() => location.reload(), 700);
    } else {
      showToast(data.error || 'Failed to update stage', 'danger');
    }
  } catch (err) {
    showToast('Network error updating lead', 'danger');
  }
}

// Prepare Quote Modal Handler
let currentQuoteLead = null;

function openPrepareQuoteModal(lead) {
  currentQuoteLead = lead;
  document.getElementById('quoteLeadId').value = lead.id;
  document.getElementById('quoteCustomerName').textContent = lead.customer_name;
  document.getElementById('quoteVehicleName').textContent = lead.vehicle_model + (lead.vehicle_reg ? ' (' + lead.vehicle_reg + ')' : '');
  document.getElementById('quoteServiceName').textContent = lead.service_name;
  
  // Set default labor
  const labor = parseFloat(lead.estimated_amount) > 0 ? parseFloat(lead.estimated_amount) : 15000;
  document.getElementById('quoteLaborCharge').value = labor;
  document.getElementById('quoteDiscount').value = 0;
  document.getElementById('quoteRemarks').value = lead.remarks || '';

  // Reset items list
  const list = document.getElementById('quoteItemsList');
  list.innerHTML = '';

  // Check if lead already has consumables
  let existing = [];
  try {
    if (lead.consumables_json) existing = JSON.parse(lead.consumables_json);
  } catch (err) {}

  if (Array.isArray(existing) && existing.length > 0) {
    existing.forEach(item => addConsumableRow(item));
  } else {
    // Add default row
    addConsumableRow();
  }

  recalculateQuoteTotal();
  openModal('modalPrepareQuote');
}

// Dynamic row addition for Store Inventory Products
function addConsumableRow(itemData = null) {
  const tbody = document.getElementById('quoteItemsList');
  if (!tbody) return;

  const inventory = window.CRM_STORE_INVENTORY || [];
  const row = document.createElement('tr');
  row.className = 'crm-quote-item-row';

  let optionsHtml = '<option value="">-- Choose Product From Store --</option>';
  inventory.forEach(inv => {
    const isSelected = itemData && (itemData.item_id == inv.id || itemData.sku == inv.sku);
    optionsHtml += `<option value="${inv.id}" data-sku="${inv.sku}" data-name="${inv.name}" data-price="${inv.sell_price || inv.unit_cost}" data-cost="${inv.unit_cost}" data-unit="${inv.unit}" data-stock="${inv.current_stock}" ${isSelected ? 'selected' : ''}>[${inv.sku}] ${inv.name} (Avail: ${inv.current_stock} ${inv.unit})</option>`;
  });

  const qty = itemData ? itemData.qty : 1.0;
  const price = itemData ? (itemData.sell_price || itemData.unit_cost) : 0;

  row.innerHTML = `
    <td>
      <select class="crm-select crm-quote-product-select" onchange="updateConsumableRowPrice(this)" required>
        ${optionsHtml}
      </select>
    </td>
    <td>
      <div style="display:flex; align-items:center; gap:4px;">
        <input type="number" step="0.1" min="0.1" class="crm-input crm-quote-qty-input" value="${qty}" oninput="recalculateQuoteTotal()" required style="width:70px;">
        <span class="crm-quote-unit-label" style="font-size:11px; color:#6b7280;">${itemData ? (itemData.unit || 'Units') : 'Units'}</span>
      </div>
    </td>
    <td>
      <div class="crm-quote-row-price" style="font-weight:700;">₹${Number(price).toLocaleString('en-IN')}</div>
      <input type="hidden" class="crm-quote-unit-price-hidden" value="${price}">
      <input type="hidden" class="crm-quote-unit-cost-hidden" value="${itemData ? itemData.unit_cost : 0}">
    </td>
    <td>
      <button type="button" class="crm-btn crm-btn-xs crm-btn-danger-ghost" onclick="removeConsumableRow(this)" title="Remove item">&times;</button>
    </td>
  `;

  tbody.appendChild(row);
  recalculateQuoteTotal();
}

function removeConsumableRow(btn) {
  const row = btn.closest('tr');
  if (row) row.remove();
  recalculateQuoteTotal();
}

function updateConsumableRowPrice(selectEl) {
  const row = selectEl.closest('tr');
  const opt = selectEl.options[selectEl.selectedIndex];
  if (!row || !opt) return;

  const price = parseFloat(opt.dataset.price || 0);
  const cost = parseFloat(opt.dataset.cost || 0);
  const unit = opt.dataset.unit || 'Units';

  const priceEl = row.querySelector('.crm-quote-row-price');
  const priceHidden = row.querySelector('.crm-quote-unit-price-hidden');
  const costHidden = row.querySelector('.crm-quote-unit-cost-hidden');
  const unitLabel = row.querySelector('.crm-quote-unit-label');

  if (priceEl) priceEl.textContent = '₹' + price.toLocaleString('en-IN');
  if (priceHidden) priceHidden.value = price;
  if (costHidden) costHidden.value = cost;
  if (unitLabel) unitLabel.textContent = unit;

  recalculateQuoteTotal();
}

function recalculateQuoteTotal() {
  const laborInput = document.getElementById('quoteLaborCharge');
  const discountInput = document.getElementById('quoteDiscount');

  const labor = parseFloat(laborInput ? laborInput.value : 0) || 0;
  const discount = parseFloat(discountInput ? discountInput.value : 0) || 0;

  let materialTotal = 0;
  const rows = document.querySelectorAll('.crm-quote-item-row');
  rows.forEach(r => {
    const qtyInput = r.querySelector('.crm-quote-qty-input');
    const priceInput = r.querySelector('.crm-quote-unit-price-hidden');
    const qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
    const price = parseFloat(priceInput ? priceInput.value : 0) || 0;
    materialTotal += (qty * price);
  });

  const subtotalBeforeGst = Math.max(0, (labor + materialTotal) - discount);
  const gst = Math.round(subtotalBeforeGst * 0.18);
  const grandTotal = subtotalBeforeGst + gst;

  document.getElementById('quoteMaterialTotal').textContent = '₹' + materialTotal.toLocaleString('en-IN');
  document.getElementById('quoteLaborTotal').textContent = '₹' + labor.toLocaleString('en-IN');
  document.getElementById('quoteGstTotal').textContent = '₹' + gst.toLocaleString('en-IN');
  document.getElementById('quoteGrandTotal').textContent = '₹' + grandTotal.toLocaleString('en-IN');
}

// Save quote and deduct store inventory
async function handleSaveQuote(e) {
  e.preventDefault();
  const form = e.target;
  const leadId = document.getElementById('quoteLeadId').value;
  const labor = parseFloat(document.getElementById('quoteLaborCharge').value || 0);
  const discount = parseFloat(document.getElementById('quoteDiscount').value || 0);
  const remarks = document.getElementById('quoteRemarks').value;

  // Gather consumables
  const consumables = [];
  const rows = document.querySelectorAll('.crm-quote-item-row');
  rows.forEach(r => {
    const select = r.querySelector('.crm-quote-product-select');
    const opt = select.options[select.selectedIndex];
    if (opt && opt.value) {
      const qty = parseFloat(r.querySelector('.crm-quote-qty-input').value || 0);
      consumables.push({
        item_id: parseInt(opt.value),
        sku: opt.dataset.sku,
        name: opt.dataset.name,
        qty: qty,
        unit: opt.dataset.unit,
        unit_cost: parseFloat(opt.dataset.cost || 0),
        sell_price: parseFloat(opt.dataset.price || 0)
      });
    }
  });

  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Generating Quote & Deducting Stock...';

  try {
    const res = await fetch('/crm/api/leads/quote', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        lead_id: leadId,
        labor_charge: labor,
        discount: discount,
        remarks: remarks,
        consumables: consumables
      })
    });
    const data = await res.json();
    if (data.success) {
      showToast(data.message || 'Quotation saved & inventory deducted from store!', 'success');
      closeModal('modalPrepareQuote');
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(data.error || 'Failed to save quote', 'danger');
    }
  } catch (err) {
    showToast('Network error saving quotation', 'danger');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Generate Quote & Deduct Inventory';
  }
}

// Convert to Booking
function openConvertBookingModal(lead) {
  document.getElementById('convertLeadId').value = lead.id;
  document.getElementById('convertBookingSub').textContent = `${lead.customer_name} • ${lead.vehicle_model} (${lead.service_name})`;
  openModal('modalConvertBooking');
}

async function handleConvertBooking(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Dispatching to Bay...';

  try {
    const res = await fetch('/crm/api/leads/convert', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    if (data.success) {
      showToast(data.message || 'Dispatched to workshop bay!', 'success');
      closeModal('modalConvertBooking');
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(data.error || 'Failed to convert booking', 'danger');
    }
  } catch (err) {
    showToast('Network error converting booking', 'danger');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Dispatch to Bay';
  }
}

// Settle Payment & Complete Job
function openPaymentSettleModal(lead) {
  document.getElementById('settleLeadId').value = lead.id;
  const total = parseFloat(lead.quote_total || lead.estimated_amount || 0);
  document.getElementById('settleBilledVal').textContent = '₹' + total.toLocaleString('en-IN');
  document.getElementById('settleClientDesc').textContent = `Customer: ${lead.customer_name} • ${lead.vehicle_model} (${lead.service_name})`;
  document.getElementById('settlePaidAmount').value = total;
  openModal('modalPaymentSettle');
}

function togglePaidAmountField(selectEl) {
  const paidGroup = document.getElementById('paidAmountGroup');
  if (selectEl.value === 'UNPAID') {
    if (paidGroup) paidGroup.style.display = 'none';
  } else {
    if (paidGroup) paidGroup.style.display = 'flex';
  }
}

async function handleSettlePayment(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Recording Payment...';

  try {
    const res = await fetch('/crm/api/leads/payment', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    if (data.success) {
      showToast(data.message || 'Payment recorded and job marked completed!', 'success');
      closeModal('modalPaymentSettle');
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(data.error || 'Failed to record payment', 'danger');
    }
  } catch (err) {
    showToast('Network error recording payment', 'danger');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Record Payment & Complete Job';
  }
}

// ==========================================
// CRM Page Navigation Alloy Wheel Transition
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
  const loader = document.getElementById('clinicPageLoader');
  if (loader) {
    // Fade out when page is ready
    loader.classList.remove('is-loading');
  }

  // Intercept internal CRM link clicks for smooth wheel transition
  document.addEventListener('click', function(e) {
    const link = e.target.closest('a');
    if (!link) return;

    const href = link.getAttribute('href');
    const target = link.getAttribute('target');

    // Skip external links, modals, anchors, mailto, tel, downloads, or new tabs
    if (!href || href.startsWith('#') || href.startsWith('javascript:') || 
        href.startsWith('tel:') || href.startsWith('mailto:') || 
        href.startsWith('https://wa.me') || target === '_blank' || 
        e.ctrlKey || e.metaKey || link.hasAttribute('download') || 
        href.includes('/export/')) {
      return;
    }

    // Skip same page
    if (href === window.location.pathname || href === window.location.href) {
      return;
    }

    // Show wheel spinner
    if (loader) {
      loader.classList.add('is-loading');
    }
  });
});


