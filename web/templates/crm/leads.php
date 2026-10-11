<?php
// THE DRIVE CLINIC — Leads Pipeline & Customer Lifecycle Kanban View
// Matches exact high-fidelity visual layout with live inventory integration, quote generation, and CSV export

$leads = Database::query("SELECT * FROM leads ORDER BY created_at DESC");
$inventoryItems = Database::query("SELECT id, sku, name, category, unit, current_stock, unit_cost, sell_price FROM inventory_items WHERE current_stock > 0 ORDER BY category ASC, name ASC");

// Group leads by stage
$colNew = [];
$colContacted = [];
$colQuote = [];
$colConverted = [];

foreach ($leads as $l) {
  $stage = strtoupper($l['stage'] ?? 'NEW');
  if ($stage === 'NEW') {
    $colNew[] = $l;
  } elseif ($stage === 'CONTACTED') {
    $colContacted[] = $l;
  } elseif ($stage === 'QUOTE_SENT') {
    $colQuote[] = $l;
  } else {
    // IN_SERVICE or COMPLETED
    $colConverted[] = $l;
  }
}
?>
<div class="crm-pipeline-page">
  <!-- Top Header Row -->
  <div class="crm-pipeline-header">
    <div class="crm-pipeline-title-group">
      <div class="crm-kicker">THE DRIVE CLINIC / STUDIO</div>
      <h1 class="crm-page-heading">Leads pipeline</h1>
      <p class="crm-page-sub">Turn first conversations into lifelong customers.</p>
    </div>
    <div class="crm-pipeline-header-actions">
      <button class="crm-btn crm-btn-primary" onclick="openAddLeadModal()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        <span>+ Add enquiry</span>
      </button>
    </div>
  </div>

  <!-- Search & CSV Export Bar -->
  <div class="crm-pipeline-toolbar">
    <div class="crm-search-box">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" id="pipelineSearch" class="crm-search-input" placeholder="Search enquiries..." oninput="filterPipelineLeads()">
    </div>
    <a href="/crm/export/leads" class="crm-btn crm-btn-secondary crm-btn-export" title="Export pipeline database to CSV">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
      <span>Export CSV</span>
    </a>
  </div>

  <!-- 4-Column Kanban Board -->
  <div class="crm-kanban-board">
    
    <!-- COLUMN 1: New enquiry -->
    <div class="crm-kanban-col" id="colNewEnquiry">
      <div class="crm-col-header">
        <span class="crm-col-title">New enquiry</span>
        <span class="crm-col-count" id="countNew"><?= count($colNew) ?></span>
      </div>
      <div class="crm-col-cards">
        <?php foreach ($colNew as $lead): ?>
          <div class="crm-lead-card" data-lead-id="<?= $lead['id'] ?>" data-search="<?= htmlspecialchars(strtolower($lead['customer_name'] . ' ' . $lead['vehicle_model'] . ' ' . $lead['service_name'] . ' ' . $lead['source'])) ?>">
            <div class="crm-lead-name"><?= htmlspecialchars($lead['customer_name']) ?></div>
            <div class="crm-lead-vehicle"><?= htmlspecialchars($lead['vehicle_model']) ?></div>
            <div class="crm-lead-service"><?= htmlspecialchars($lead['service_name']) ?></div>
            <div class="crm-lead-amount">₹<?= number_format((float)$lead['estimated_amount']) ?></div>
            <div class="crm-lead-source-row">
              <span class="crm-source-pill"><?= htmlspecialchars($lead['source'] ?? 'Website') ?></span>
            </div>
            <button class="crm-card-action-btn" onclick="updateLeadStage(<?= $lead['id'] ?>, 'CONTACTED')">
              Mark contacted
            </button>
          </div>
        <?php endforeach; ?>
        <?php if (empty($colNew)): ?>
          <div class="crm-col-empty">No new enquiries</div>
        <?php endif; ?>
      </div>
    </div>

    <!-- COLUMN 2: Contacted -->
    <div class="crm-kanban-col" id="colContacted">
      <div class="crm-col-header">
        <span class="crm-col-title">Contacted</span>
        <span class="crm-col-count" id="countContacted"><?= count($colContacted) ?></span>
      </div>
      <div class="crm-col-cards">
        <?php foreach ($colContacted as $lead): ?>
          <div class="crm-lead-card" data-lead-id="<?= $lead['id'] ?>" data-search="<?= htmlspecialchars(strtolower($lead['customer_name'] . ' ' . $lead['vehicle_model'] . ' ' . $lead['service_name'] . ' ' . $lead['source'])) ?>">
            <div class="crm-lead-name"><?= htmlspecialchars($lead['customer_name']) ?></div>
            <div class="crm-lead-vehicle"><?= htmlspecialchars($lead['vehicle_model']) ?></div>
            <div class="crm-lead-service"><?= htmlspecialchars($lead['service_name']) ?></div>
            <div class="crm-lead-amount">₹<?= number_format((float)$lead['estimated_amount']) ?></div>
            <div class="crm-lead-source-row">
              <span class="crm-source-pill"><?= htmlspecialchars($lead['source'] ?? 'Website') ?></span>
            </div>
            <button class="crm-card-action-btn" onclick="openPrepareQuoteModal(<?= htmlspecialchars(json_encode($lead)) ?>)">
              Prepare quote
            </button>
          </div>
        <?php endforeach; ?>
        <?php if (empty($colContacted)): ?>
          <div class="crm-col-empty">No contacted leads</div>
        <?php endif; ?>
      </div>
    </div>

    <!-- COLUMN 3: Quote sent -->
    <div class="crm-kanban-col" id="colQuoteSent">
      <div class="crm-col-header">
        <span class="crm-col-title">Quote sent</span>
        <span class="crm-col-count" id="countQuote"><?= count($colQuote) ?></span>
      </div>
      <div class="crm-col-cards">
        <?php foreach ($colQuote as $lead): ?>
          <div class="crm-lead-card" data-lead-id="<?= $lead['id'] ?>" data-search="<?= htmlspecialchars(strtolower($lead['customer_name'] . ' ' . $lead['vehicle_model'] . ' ' . $lead['service_name'] . ' ' . $lead['source'])) ?>">
            <div class="crm-lead-name"><?= htmlspecialchars($lead['customer_name']) ?></div>
            <div class="crm-lead-vehicle"><?= htmlspecialchars($lead['vehicle_model']) ?></div>
            <div class="crm-lead-service"><?= htmlspecialchars($lead['service_name']) ?></div>
            <div class="crm-lead-amount">₹<?= number_format((float)($lead['quote_total'] ?: $lead['estimated_amount'])) ?></div>
            <div class="crm-lead-source-row">
              <span class="crm-source-pill"><?= htmlspecialchars($lead['source'] ?? 'Website') ?></span>
              <span class="crm-pill crm-pill-warning" style="font-size:10px; padding:1px 5px;">Quote Sent</span>
            </div>
            <button class="crm-card-action-btn" onclick="openConvertBookingModal(<?= htmlspecialchars(json_encode($lead)) ?>)">
              Convert to booking
            </button>
          </div>
        <?php endforeach; ?>
        <?php if (empty($colQuote)): ?>
          <div class="crm-col-empty">No sent quotes</div>
        <?php endif; ?>
      </div>
    </div>

    <!-- COLUMN 4: Converted / Under Service & Completed -->
    <div class="crm-kanban-col" id="colConverted">
      <div class="crm-col-header">
        <span class="crm-col-title">Converted & Active</span>
        <span class="crm-col-count" id="countConverted"><?= count($colConverted) ?></span>
      </div>
      <div class="crm-col-cards">
        <?php foreach ($colConverted as $lead): 
          $isCompleted = ($lead['stage'] === 'COMPLETED');
          $isPaid = ($lead['payment_status'] === 'PAID');
        ?>
          <div class="crm-lead-card <?= $isCompleted ? 'crm-card-completed' : 'crm-card-active-job' ?>" data-lead-id="<?= $lead['id'] ?>" data-search="<?= htmlspecialchars(strtolower($lead['customer_name'] . ' ' . $lead['vehicle_model'] . ' ' . $lead['service_name'] . ' ' . $lead['source'])) ?>">
            <div class="crm-lead-name"><?= htmlspecialchars($lead['customer_name']) ?></div>
            <div class="crm-lead-vehicle"><?= htmlspecialchars($lead['vehicle_model']) ?> <?= $lead['vehicle_reg'] ? '(' . htmlspecialchars($lead['vehicle_reg']) . ')' : '' ?></div>
            <div class="crm-lead-service"><?= htmlspecialchars($lead['service_name']) ?></div>
            <div class="crm-lead-amount">₹<?= number_format((float)($lead['quote_total'] ?: $lead['estimated_amount'])) ?></div>
            
            <div class="crm-lead-source-row" style="margin-bottom: 8px;">
              <span class="crm-source-pill"><?= htmlspecialchars($lead['source'] ?? 'Website') ?></span>
              <?php if ($isPaid): ?>
                <span class="crm-pill crm-pill-success" style="font-size:10px; padding:1px 5px;">Paid (<?= htmlspecialchars($lead['payment_method'] ?: 'UPI') ?>)</span>
              <?php elseif ($lead['payment_status'] === 'PARTIALLY_PAID'): ?>
                <span class="crm-pill crm-pill-warning" style="font-size:10px; padding:1px 5px;">Adv ₹<?= number_format((float)$lead['paid_amount']) ?></span>
              <?php else: ?>
                <span class="crm-pill crm-pill-danger" style="font-size:10px; padding:1px 5px;">Unpaid</span>
              <?php endif; ?>
            </div>

            <?php if (!$isPaid || !$isCompleted): ?>
              <button class="crm-card-action-btn crm-btn-settle" onclick="openPaymentSettleModal(<?= htmlspecialchars(json_encode($lead)) ?>)">
                <?= $isCompleted ? '💳 Settle Payment' : '⚙️ Bay Status & Payment' ?>
              </button>
            <?php else: ?>
              <button class="crm-card-action-btn crm-btn-view-invoice" onclick="alert('Viewing paid invoice for <?= addslashes($lead['customer_name']) ?> (Grand Total: ₹<?= number_format((float)$lead['quote_total']) ?>)')">
                ✅ Paid & Delivered
              </button>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
        <?php if (empty($colConverted)): ?>
          <div class="crm-col-empty">No active jobs yet</div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<!-- ==========================================
     MODALS FOR LEADS WORKFLOW
     ========================================== -->

<!-- 1. Add New Enquiry Modal -->
<div class="crm-modal-backdrop" id="modalAddLead">
  <div class="crm-modal crm-modal-md">
    <div class="crm-modal-header">
      <div class="crm-modal-title-group">
        <h3 class="crm-modal-title">Add Customer Enquiry</h3>
        <p class="crm-modal-sub">Log studio walk-ins, phone calls, Instagram, or referral leads.</p>
      </div>
      <button class="crm-modal-close" onclick="closeModal('modalAddLead')">&times;</button>
    </div>
    <form id="formAddLead" onsubmit="handleCreateLead(event)">
      <div class="crm-modal-body">
        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Customer Name *</label>
            <input type="text" name="customer_name" class="crm-input" placeholder="e.g. Karan Malhotra" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Phone / WhatsApp *</label>
            <input type="tel" name="phone" class="crm-input" placeholder="e.g. +91 98123 45670" required>
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Vehicle Model *</label>
            <input type="text" name="vehicle_model" class="crm-input" placeholder="e.g. Audi A4 / Mahindra XUV700" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Registration No. (Optional)</label>
            <input type="text" name="vehicle_reg" class="crm-input" placeholder="e.g. JK 02 CD 9090">
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Service Package Requested *</label>
            <select name="service_name" class="crm-select" required>
              <option value="Full-body PPF">Full-body PPF (STEK DynoShield)</option>
              <option value="Ceramic shield">Ceramic shield 9H+ (Gtechniq CSL)</option>
              <option value="Signature detailing">Signature Detailing & Paint Correction</option>
              <option value="Precision bike detailing">Precision Bike Detailing</option>
              <option value="Premium wash">Premium Foam Wash & Steam Decon</option>
              <option value="Custom Detailing">Custom Package / Add-ons</option>
            </select>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Estimated Value (₹) *</label>
            <input type="number" step="100" name="estimated_amount" class="crm-input" placeholder="e.g. 95000" required>
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Enquiry Source *</label>
            <select name="source" class="crm-select" required>
              <option value="Walk-in">Walk-in</option>
              <option value="Instagram">Instagram</option>
              <option value="Website">Website Form</option>
              <option value="Referral">Customer Referral</option>
              <option value="Phone">Direct Phone Call</option>
            </select>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Initial Pipeline Stage</label>
            <select name="stage" class="crm-select">
              <option value="NEW">New enquiry</option>
              <option value="CONTACTED">Contacted</option>
              <option value="QUOTE_SENT">Quote sent</option>
            </select>
          </div>
        </div>

        <div class="crm-form-group">
          <label class="crm-label">Customer Remarks / Inspection Notes</label>
          <textarea name="remarks" class="crm-textarea" placeholder="Note down special requirements (e.g. Swirl marks on hood, requested Saturday delivery)"></textarea>
        </div>
      </div>
      <div class="crm-modal-footer">
        <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalAddLead')">Cancel</button>
        <button type="submit" class="crm-btn crm-btn-primary">Add to Pipeline</button>
      </div>
    </form>
  </div>
</div>

<!-- 2. Prepare Quote & Deduct Inventory Consumables Modal (KEY USER REQUIREMENT) -->
<div class="crm-modal-backdrop" id="modalPrepareQuote">
  <div class="crm-modal crm-modal-lg">
    <div class="crm-modal-header">
      <div class="crm-modal-title-group">
        <h3 class="crm-modal-title">Prepare Official Quotation & Allocate Store Consumables</h3>
        <p class="crm-modal-sub" id="quoteLeadSubtitle">Select products used from inventory to compute quote and deduct from store stock.</p>
      </div>
      <button class="crm-modal-close" onclick="closeModal('modalPrepareQuote')">&times;</button>
    </div>
    <form id="formPrepareQuote" onsubmit="handleSaveQuote(event)">
      <input type="hidden" name="lead_id" id="quoteLeadId">
      <div class="crm-modal-body">
        
        <!-- Customer / Vehicle Snapshot -->
        <div class="crm-quote-summary-box" id="quoteClientSummary">
          <div class="crm-summary-item">
            <span>Customer:</span>
            <strong id="quoteCustomerName">—</strong>
          </div>
          <div class="crm-summary-item">
            <span>Vehicle:</span>
            <strong id="quoteVehicleName">—</strong>
          </div>
          <div class="crm-summary-item">
            <span>Service:</span>
            <strong id="quoteServiceName">—</strong>
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Base Detailing Labor / Service Charge (₹)</label>
            <input type="number" step="100" id="quoteLaborCharge" name="labor_charge" class="crm-input" value="15000" oninput="recalculateQuoteTotal()">
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Special Discount (₹)</label>
            <input type="number" step="100" id="quoteDiscount" name="discount" class="crm-input" value="0" oninput="recalculateQuoteTotal()">
          </div>
        </div>

        <!-- Inventory Product Allocation Table -->
        <div class="crm-quote-consumables-section">
          <div class="crm-section-head-row">
            <div>
              <label class="crm-label" style="font-size:12px; margin-bottom:0;">📦 Allocate Products From Store Inventory (Auto-deducted upon dispatch)</label>
              <p style="font-size:11px; color:#6b7280; margin:2px 0 0;">Product cost will be calculated and quantity will be written down from store catalog.</p>
            </div>
            <button type="button" class="crm-btn crm-btn-xs crm-btn-secondary" onclick="addConsumableRow()">+ Add Product</button>
          </div>

          <table class="crm-table crm-quote-items-table" id="quoteItemsTable">
            <thead>
              <tr>
                <th style="width: 45%;">Select Store Product</th>
                <th style="width: 20%;">Qty Used</th>
                <th style="width: 25%;">Unit Retail Price</th>
                <th style="width: 10%;">Action</th>
              </tr>
            </thead>
            <tbody id="quoteItemsList">
              <!-- Dynamically populated rows -->
            </tbody>
          </table>
        </div>

        <!-- Real-time Quote Total Breakdown Box -->
        <div class="crm-quote-total-card">
          <div class="crm-total-line">
            <span>Material / Consumables Total:</span>
            <strong id="quoteMaterialTotal">₹0</strong>
          </div>
          <div class="crm-total-line">
            <span>Labor & Detailing Work:</span>
            <strong id="quoteLaborTotal">₹15,000</strong>
          </div>
          <div class="crm-total-line">
            <span>GST (18% Included):</span>
            <span id="quoteGstTotal">₹2,288</span>
          </div>
          <div class="crm-total-line crm-grand-total">
            <span>Final Quotation Total:</span>
            <strong id="quoteGrandTotal">₹15,000</strong>
          </div>
        </div>

        <div class="crm-form-group">
          <label class="crm-label">Quotation Remarks / Warranty Terms</label>
          <textarea name="remarks" id="quoteRemarks" class="crm-textarea" placeholder="Include warranty coverage (e.g. 5-Year STEK warranty with 1 annual top-coat checkup included)"></textarea>
        </div>
      </div>
      <div class="crm-modal-footer">
        <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalPrepareQuote')">Cancel</button>
        <button type="submit" class="crm-btn crm-btn-primary">Generate Quote & Deduct Inventory</button>
      </div>
    </form>
  </div>
</div>

<!-- 3. Convert to Booking & Bay Assignment Modal -->
<div class="crm-modal-backdrop" id="modalConvertBooking">
  <div class="crm-modal crm-modal-md">
    <div class="crm-modal-header">
      <div class="crm-modal-title-group">
        <h3 class="crm-modal-title">Convert Quote to Active Studio Booking</h3>
        <p class="crm-modal-sub" id="convertBookingSub">Assign workshop bay and lead detailer.</p>
      </div>
      <button class="crm-modal-close" onclick="closeModal('modalConvertBooking')">&times;</button>
    </div>
    <form id="formConvertBooking" onsubmit="handleConvertBooking(event)">
      <input type="hidden" name="lead_id" id="convertLeadId">
      <div class="crm-modal-body">
        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Workshop Bay Allocation *</label>
            <select name="bay_no" class="crm-select" required>
              <option value="1">Bay 01 - Dust-Free PPF Cleanroom</option>
              <option value="2">Bay 02 - Ceramic & Paint Correction</option>
              <option value="3">Bay 03 - Precision Wash & Decon</option>
              <option value="4">Bay 04 - Ready / Final Delivery</option>
            </select>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Assigned Lead Detailer *</label>
            <select name="assigned_tech" class="crm-select" required>
              <option value="Lead PPF Specialist">Lead PPF Specialist</option>
              <option value="Master Ceramic Detailer">Master Ceramic Detailer</option>
              <option value="Senior Paint Correction Specialist">Senior Paint Correction Specialist</option>
              <option value="Precision Wash Technician">Precision Wash Technician</option>
            </select>
          </div>
        </div>
        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Scheduled Date *</label>
            <input type="date" name="date" class="crm-input" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Time Slot *</label>
            <select name="time" class="crm-select" required>
              <option value="10:00 AM">10:00 AM</option>
              <option value="12:00 PM">12:00 PM</option>
              <option value="02:00 PM">02:00 PM</option>
              <option value="04:00 PM">04:00 PM</option>
            </select>
          </div>
        </div>
      </div>
      <div class="crm-modal-footer">
        <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalConvertBooking')">Cancel</button>
        <button type="submit" class="crm-btn crm-btn-primary">Dispatch to Bay</button>
      </div>
    </form>
  </div>
</div>

<!-- 4. Payment Settlement & Job Completion Modal (KEY USER REQUIREMENT) -->
<div class="crm-modal-backdrop" id="modalPaymentSettle">
  <div class="crm-modal crm-modal-md">
    <div class="crm-modal-header crm-header-success">
      <div class="crm-modal-title-group">
        <h3 class="crm-modal-title">💳 Complete Service & Settle Payment</h3>
        <p class="crm-modal-sub" id="settleLeadSub">Mark payment mode and record settlement in CRM.</p>
      </div>
      <button class="crm-modal-close" onclick="closeModal('modalPaymentSettle')">&times;</button>
    </div>
    <form id="formPaymentSettle" onsubmit="handleSettlePayment(event)">
      <input type="hidden" name="lead_id" id="settleLeadId">
      <div class="crm-modal-body">
        
        <div class="crm-decline-loss-card" style="background:#f0fdf4; border-color:#86efac;">
          <div class="crm-loss-row">
            <span class="crm-loss-label" style="color:#15803d;">Total Job Billed Amount:</span>
            <span class="crm-loss-value" id="settleBilledVal" style="color:#15803d;">₹0</span>
          </div>
          <div class="crm-loss-sub" id="settleClientDesc" style="color:#166534;">Customer: —</div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Payment Status *</label>
            <select name="payment_status" id="settleStatusSelect" class="crm-select" required onchange="togglePaidAmountField(this)">
              <option value="PAID">Fully Paid (100% Settled)</option>
              <option value="PARTIALLY_PAID">Partially Paid (Advance Deposit)</option>
              <option value="UNPAID">Unpaid / Payment Pending</option>
            </select>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Payment Method / Mode *</label>
            <select name="payment_method" class="crm-select" required>
              <option value="UPI">UPI (Google Pay / PhonePe / Paytm)</option>
              <option value="POS Card">POS Card Swipe (Debit/Credit)</option>
              <option value="Cash">Cash Collection</option>
              <option value="Net Banking">Net Banking / IMPS</option>
            </select>
          </div>
        </div>

        <div class="crm-form-group" id="paidAmountGroup">
          <label class="crm-label">Paid Amount Received (₹) *</label>
          <input type="number" step="100" name="paid_amount" id="settlePaidAmount" class="crm-input" required>
        </div>

        <div class="crm-form-group">
          <label class="crm-label">Job Completion Status</label>
          <select name="stage" class="crm-select">
            <option value="COMPLETED">Service Finished & Delivered (QC Passed)</option>
            <option value="IN_SERVICE">Still in Workshop Bay</option>
          </select>
        </div>
      </div>
      <div class="crm-modal-footer">
        <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalPaymentSettle')">Cancel</button>
        <button type="submit" class="crm-btn crm-btn-success">Record Payment & Complete Job</button>
      </div>
    </form>
  </div>
</div>

<!-- Pass Inventory Catalog into JS for Quote Calculator -->
<script>
  window.CRM_STORE_INVENTORY = <?= json_encode($inventoryItems) ?>;
</script>
