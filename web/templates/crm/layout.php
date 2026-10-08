<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'THE DRIVE CLINIC — Studio CRM') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/crm.css">
  <link rel="icon" href="/assets/favicon.ico" type="image/x-icon">
</head>
<body class="crm-body">
  <div class="crm-app">
    <!-- Sidebar Navigation -->
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <!-- Main Workspace Viewport -->
    <div class="crm-main-viewport">
      <!-- Topbar Header -->
      <?php require __DIR__ . '/partials/header.php'; ?>

      <!-- View Content Slot -->
      <main class="crm-content">
        <?php if (isset($viewFile) && file_exists($viewFile)) {
          require $viewFile;
        } else {
          echo "<div class='crm-container'><div class='crm-card'><h3>Page view not found</h3></div></div>";
        } ?>
      </main>
    </div>
  </div>

  <!-- Global CRM Modals -->
  
  <!-- 1. Quick New Booking Modal -->
  <div class="crm-modal-backdrop" id="modalNewBooking">
    <div class="crm-modal crm-modal-md">
      <div class="crm-modal-header">
        <div class="crm-modal-title-group">
          <h3 class="crm-modal-title">Create Studio Booking</h3>
          <p class="crm-modal-sub">Schedule car or bike detailing, PPF, or wash appointment.</p>
        </div>
        <button class="crm-modal-close" onclick="closeModal('modalNewBooking')">&times;</button>
      </div>
      <form id="formNewBooking" onsubmit="handleCreateBooking(event)">
        <div class="crm-modal-body">
          <div class="crm-form-row">
            <div class="crm-form-group">
              <label class="crm-label">Customer Name *</label>
              <input type="text" name="customer_name" class="crm-input" placeholder="e.g. Vikram Sharma" required>
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Phone Number *</label>
              <input type="tel" name="customer_phone" class="crm-input" placeholder="e.g. +91 98765 43210" required>
            </div>
          </div>
          <div class="crm-form-row">
            <div class="crm-form-group">
              <label class="crm-label">Vehicle Brand & Model *</label>
              <input type="text" name="vehicle_model" class="crm-input" placeholder="e.g. BMW 320d / Thar Roxx" required>
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Registration No.</label>
              <input type="text" name="vehicle_reg" class="crm-input" placeholder="e.g. JK 02 CQ 7788">
            </div>
          </div>
          <div class="crm-form-row">
            <div class="crm-form-group">
              <label class="crm-label">Service Package *</label>
              <select name="service_id" class="crm-select" required>
                <option value="1">Paint Protection Film (PPF) - STEK DynoShield</option>
                <option value="2">Ceramic Coating 9H+ (Gtechniq Crystal Serum)</option>
                <option value="3">Precision Bike Detailing & Matte Care</option>
                <option value="4">Signature Wash & Steam Sanitization</option>
                <option value="5">Interior Deep Steam Restoration</option>
              </select>
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Bay Assignment</label>
              <select name="bay_no" class="crm-select">
                <option value="1">Bay 01 - Dust-Free PPF Cleanroom</option>
                <option value="2">Bay 02 - Ceramic & Paint Correction</option>
                <option value="3">Bay 03 - Precision Wash & Decon</option>
                <option value="4">Bay 04 - Ready / Inspection</option>
              </select>
            </div>
          </div>
          <div class="crm-form-row">
            <div class="crm-form-group">
              <label class="crm-label">Appointment Date *</label>
              <input type="date" name="date" class="crm-input" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Time Slot *</label>
              <select name="time" class="crm-select" required>
                <option value="09:30">09:30 AM</option>
                <option value="10:00" selected>10:00 AM</option>
                <option value="11:30">11:30 AM</option>
                <option value="14:00">02:00 PM</option>
                <option value="15:30">03:30 PM</option>
                <option value="17:00">05:00 PM</option>
              </select>
            </div>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Studio Notes / Special Requests</label>
            <textarea name="notes" class="crm-textarea" placeholder="e.g. Customer requested headlight tint & scratch inspection"></textarea>
          </div>
        </div>
        <div class="crm-modal-footer">
          <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalNewBooking')">Cancel</button>
          <button type="submit" class="crm-btn crm-btn-primary">Confirm & Dispatch</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 2. Inventory: Add New Product in Store Modal -->
  <div class="crm-modal-backdrop" id="modalAddProduct">
    <div class="crm-modal crm-modal-md">
      <div class="crm-modal-header">
        <div class="crm-modal-title-group">
          <h3 class="crm-modal-title">Add New Product to Store Catalog</h3>
          <p class="crm-modal-sub">Register detailing chemicals, PPF rolls, compounds or pads.</p>
        </div>
        <button class="crm-modal-close" onclick="closeModal('modalAddProduct')">&times;</button>
      </div>
      <form id="formAddProduct" onsubmit="handleAddProduct(event)">
        <div class="crm-modal-body">
          <div class="crm-form-row">
            <div class="crm-form-group">
              <label class="crm-label">Product SKU *</label>
              <input type="text" name="sku" class="crm-input" placeholder="e.g. PPF-STEK-15M" required>
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Category *</label>
              <select name="category" class="crm-select" required>
                <option value="PPF">PPF & Vinyl Films</option>
                <option value="COATING">Ceramic & Graphene Coatings</option>
                <option value="COMPOUND">Polishing Compounds & Pastes</option>
                <option value="WASH">Shampoos & Chemicals</option>
                <option value="PADS">Buffing Pads & Microfibers</option>
                <option value="EQUIPMENT">Studio Tools & Sprayers</option>
              </select>
            </div>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Product Name *</label>
            <input type="text" name="name" class="crm-input" placeholder="e.g. STEK DYNOshield Gloss PPF Roll (15m x 1.52m)" required>
          </div>
          <div class="crm-form-row">
            <div class="crm-form-group">
              <label class="crm-label">Initial Stock Qty *</label>
              <input type="number" step="0.1" name="current_stock" class="crm-input" placeholder="0" required>
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Unit of Measure *</label>
              <select name="unit" class="crm-select" required>
                <option value="Rolls">Rolls (15m / 30m)</option>
                <option value="Bottles">Bottles (50ml / 500ml)</option>
                <option value="Liters">Liters / Gallons</option>
                <option value="Packs">Packs / Pieces</option>
                <option value="Units">Units</option>
              </select>
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Min Alert Threshold *</label>
              <input type="number" step="0.1" name="min_stock_alert" class="crm-input" value="2" required>
            </div>
          </div>
          <div class="crm-form-row">
            <div class="crm-form-group">
              <label class="crm-label">Purchase Unit Cost (₹) *</label>
              <input type="number" step="0.01" name="unit_cost" class="crm-input" placeholder="e.g. 48500" required>
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Retail / Bill Price (₹)</label>
              <input type="number" step="0.01" name="sell_price" class="crm-input" placeholder="e.g. 75000">
            </div>
          </div>
          <div class="crm-form-row">
            <div class="crm-form-group">
              <label class="crm-label">Supplier / Brand</label>
              <input type="text" name="supplier" class="crm-input" placeholder="e.g. STEK Automotive India">
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Storage Bay / Bin Location</label>
              <input type="text" name="location" class="crm-input" placeholder="e.g. Bay 1 Climate Cabinet #A2">
            </div>
          </div>
        </div>
        <div class="crm-modal-footer">
          <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalAddProduct')">Cancel</button>
          <button type="submit" class="crm-btn crm-btn-primary">Add to Store Inventory</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 3. Inventory: Decline / Wastage / Scrap Logger Modal (USER KEY REQUIREMENT) -->
  <div class="crm-modal-backdrop" id="modalDeclineStock">
    <div class="crm-modal crm-modal-md">
      <div class="crm-modal-header crm-header-danger">
        <div class="crm-modal-title-group">
          <h3 class="crm-modal-title">⚠️ Log Inventory Decline / Wastage</h3>
          <p class="crm-modal-sub">Record damaged stock, cutting errors, expired coatings, or scrap with loss audit trail.</p>
        </div>
        <button class="crm-modal-close" onclick="closeModal('modalDeclineStock')">&times;</button>
      </div>
      <form id="formDeclineStock" onsubmit="handleDeclineStock(event)">
        <div class="crm-modal-body">
          <div class="crm-form-group">
            <label class="crm-label">Select Product *</label>
            <select name="item_id" id="declineProductSelect" class="crm-select" required onchange="updateDeclineProductDetails(this)">
              <option value="">-- Choose Stock Item --</option>
              <?php
              $allItems = Database::query("SELECT id, sku, name, unit, current_stock, unit_cost FROM inventory_items ORDER BY name ASC");
              foreach ($allItems as $it):
              ?>
                <option value="<?= $it['id'] ?>" data-unit="<?= htmlspecialchars($it['unit']) ?>" data-stock="<?= $it['current_stock'] ?>" data-cost="<?= $it['unit_cost'] ?>">
                  [<?= htmlspecialchars($it['sku']) ?>] <?= htmlspecialchars($it['name']) ?> (Avail: <?= $it['current_stock'] ?> <?= htmlspecialchars($it['unit']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="crm-form-row">
            <div class="crm-form-group">
              <label class="crm-label">Decline Reason / Category *</label>
              <select name="decline_type" class="crm-select" required>
                <option value="DECLINE_DAMAGED">Damaged in Transit / Unboxing</option>
                <option value="DECLINE_SCRAP">Cutting / Installation Scrap (Film Crease / Stretch)</option>
                <option value="DECLINE_EXPIRED">Expired / Crystallized Coating Bottle</option>
                <option value="AUDIT_ADJUST">Physical Stock Audit Discrepancy (Missing)</option>
                <option value="DECLINE_DEFECTIVE">Manufacturer Defect (Pending RMA Return)</option>
              </select>
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Quantity to Decline *</label>
              <div class="crm-input-addon-group">
                <input type="number" step="0.1" min="0.1" name="quantity" id="declineQtyInput" class="crm-input" placeholder="0.0" required oninput="calculateDeclineLoss()">
                <span class="crm-input-addon" id="declineUnitLabel">Units</span>
              </div>
            </div>
          </div>

          <div class="crm-decline-loss-card" id="declineLossCard">
            <div class="crm-loss-row">
              <span class="crm-loss-label">Estimated Financial Loss Value:</span>
              <span class="crm-loss-value" id="declineLossVal">₹0.00</span>
            </div>
            <div class="crm-loss-sub" id="declineStockRemaining">Remaining stock after decline: --</div>
          </div>

          <div class="crm-form-row">
            <div class="crm-form-group">
              <label class="crm-label">Responsible Technician / Logged By *</label>
              <select name="logged_by" class="crm-select" required>
                <option value="Rohan Sharma (Master PPF Lead)">Rohan Sharma (Master PPF Lead)</option>
                <option value="Arjun Mehta (Lead Detailer)">Arjun Mehta (Lead Detailer)</option>
                <option value="Sameer Khan (Wash Specialist)">Sameer Khan (Wash Specialist)</option>
                <option value="Amit Kapoor (Studio Owner)">Amit Kapoor (Studio Owner)</option>
              </select>
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Reference / Job Card # (Optional)</label>
              <input type="text" name="reference_no" class="crm-input" placeholder="e.g. JC-2026-041 or PO-992">
            </div>
          </div>

          <div class="crm-form-group">
            <label class="crm-label">Detailed Decline Reason & Incident Note *</label>
            <textarea name="reason" class="crm-textarea" placeholder="Explain what happened (e.g. Dust speck contamination during front bumper PPF stretch or Bottle opened 6 months ago and crystallized)" required></textarea>
          </div>
        </div>
        <div class="crm-modal-footer">
          <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalDeclineStock')">Cancel</button>
          <button type="submit" class="crm-btn crm-btn-danger">Confirm Decline & Write-Off</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 4. Inventory: Stock In (Purchase / Restock) Modal -->
  <div class="crm-modal-backdrop" id="modalStockIn">
    <div class="crm-modal crm-modal-md">
      <div class="crm-modal-header">
        <div class="crm-modal-title-group">
          <h3 class="crm-modal-title">Stock In / Purchase Restock</h3>
          <p class="crm-modal-sub">Receive newly delivered inventory into studio storage.</p>
        </div>
        <button class="crm-modal-close" onclick="closeModal('modalStockIn')">&times;</button>
      </div>
      <form id="formStockIn" onsubmit="handleStockIn(event)">
        <div class="crm-modal-body">
          <div class="crm-form-group">
            <label class="crm-label">Select Product *</label>
            <select name="item_id" class="crm-select" required>
              <option value="">-- Choose Stock Item --</option>
              <?php foreach ($allItems as $it): ?>
                <option value="<?= $it['id'] ?>">
                  [<?= htmlspecialchars($it['sku']) ?>] <?= htmlspecialchars($it['name']) ?> (Current: <?= $it['current_stock'] ?> <?= htmlspecialchars($it['unit']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="crm-form-row">
            <div class="crm-form-group">
              <label class="crm-label">Received Quantity *</label>
              <input type="number" step="0.1" min="0.1" name="quantity" class="crm-input" placeholder="0.0" required>
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Unit Purchase Cost (₹) *</label>
              <input type="number" step="0.01" name="unit_cost" class="crm-input" placeholder="e.g. 48500" required>
            </div>
          </div>
          <div class="crm-form-row">
            <div class="crm-form-group">
              <label class="crm-label">Supplier Invoice / PO #</label>
              <input type="text" name="reference_no" class="crm-input" placeholder="e.g. INV-STEK-88192">
            </div>
            <div class="crm-form-group">
              <label class="crm-label">Received By</label>
              <input type="text" name="logged_by" class="crm-input" value="Amit Kapoor (Studio Admin)" required>
            </div>
          </div>
        </div>
        <div class="crm-modal-footer">
          <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalStockIn')">Cancel</button>
          <button type="submit" class="crm-btn crm-btn-primary">Add Received Stock</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Toast Notification Container -->
  <div class="crm-toast-container" id="crmToastContainer"></div>

  <!-- Automotive Alloy Wheel Page Transition Overlay -->
  <?php require dirname(__DIR__) . '/partials/wheel-loader.php'; ?>

  <!-- CRM JavaScript Controller -->
  <script src="/assets/js/crm.js"></script>
</body>
</html>
