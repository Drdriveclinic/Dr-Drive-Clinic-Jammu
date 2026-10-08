<?php
// THE DRIVE CLINIC — Inventory & Store Management View
// Features: Product Catalog, Stock-In, Decline & Wastage Logger, Loss Ledger, Minimum Threshold Alerts

$items = Database::query("SELECT * FROM inventory_items ORDER BY category ASC, name ASC");
$declines = Database::query("
  SELECT t.*, i.name as item_name, i.sku, i.unit, i.category 
  FROM inventory_transactions t
  JOIN inventory_items i ON t.item_id = i.id
  WHERE t.type LIKE 'DECLINE_%' OR t.type = 'AUDIT_ADJUST'
  ORDER BY t.created_at DESC
");

$allTransactions = Database::query("
  SELECT t.*, i.name as item_name, i.sku, i.unit, i.category 
  FROM inventory_transactions t
  JOIN inventory_items i ON t.item_id = i.id
  ORDER BY t.created_at DESC
  LIMIT 50
");

// Calculate stats
$totalItems = count($items);
$totalValuationCost = 0;
$totalValuationRetail = 0;
$lowStockCount = 0;

foreach ($items as $it) {
  $totalValuationCost += ($it['current_stock'] * $it['unit_cost']);
  $totalValuationRetail += ($it['current_stock'] * ($it['sell_price'] ?: $it['unit_cost']));
  if ($it['current_stock'] <= $it['min_stock_alert']) {
    $lowStockCount++;
  }
}

$totalDeclineLoss = 0;
foreach ($declines as $d) {
  $totalDeclineLoss += abs((float)$d['total_cost']);
}
?>
<div class="crm-inventory-page">
  <!-- Top Title & Action Header -->
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">OPERATIONS &bull; STORE MANAGEMENT</div>
      <h1 class="crm-page-heading">Inventory & Decline Management</h1>
      <p class="crm-page-sub">Track detailing chemicals, PPF rolls, compounds, stock-ins, and scrap/decline audit trail.</p>
    </div>
    <div class="crm-page-actions">
      <button class="crm-btn crm-btn-secondary" onclick="openStockInModal()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"></path></svg>
        <span>+ Stock In (Purchase)</span>
      </button>
      <button class="crm-btn crm-btn-danger" onclick="openDeclineStockModal()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        <span>⚠️ Log Decline / Wastage</span>
      </button>
      <button class="crm-btn crm-btn-primary" onclick="openAddProductModal()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
        <span>+ Add Product in Store</span>
      </button>
    </div>
  </div>

  <!-- Inventory Metric Cards -->
  <div class="crm-stats-grid crm-inventory-stats">
    <div class="crm-stat-card">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Store SKUs Catalog</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
        </div>
      </div>
      <div class="crm-stat-value"><?= $totalItems ?> SKUs</div>
      <div class="crm-stat-footer">Active studio products</div>
    </div>

    <div class="crm-stat-card">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Total Stock Valuation</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        </div>
      </div>
      <div class="crm-stat-value">₹<?= number_format($totalValuationCost) ?></div>
      <div class="crm-stat-footer">Retail value: ₹<?= number_format($totalValuationRetail) ?></div>
    </div>

    <div class="crm-stat-card <?= $lowStockCount > 0 ? 'crm-stat-warning' : '' ?>">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Low Stock Alerts</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        </div>
      </div>
      <div class="crm-stat-value"><?= $lowStockCount ?> <?= $lowStockCount === 1 ? 'Item' : 'Items' ?></div>
      <div class="crm-stat-footer">Below minimum safety threshold</div>
    </div>

    <div class="crm-stat-card crm-stat-danger">
      <div class="crm-stat-header">
        <span class="crm-stat-label">Total Declines & Loss</span>
        <div class="crm-stat-icon-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline><polyline points="17 18 23 18 23 12"></polyline></svg>
        </div>
      </div>
      <div class="crm-stat-value">₹<?= number_format($totalDeclineLoss) ?></div>
      <div class="crm-stat-footer"><?= count($declines) ?> incident logs recorded</div>
    </div>
  </div>

  <!-- Tab Navigation for Inventory -->
  <div class="crm-tabs-row">
    <div class="crm-tabs">
      <button class="crm-tab-btn active" onclick="switchInventoryTab('catalog', this)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h7"></path></svg>
        <span>Store Inventory Catalog (<?= $totalItems ?>)</span>
      </button>
      <button class="crm-tab-btn" onclick="switchInventoryTab('declines', this)" id="tabDeclinesBtn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path></svg>
        <span>⚠️ Decline & Wastage Audit Ledger (<?= count($declines) ?>)</span>
      </button>
      <button class="crm-tab-btn" onclick="switchInventoryTab('ledger', this)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        <span>Stock Movements Ledger</span>
      </button>
    </div>

    <!-- Live Search & Filter -->
    <div class="crm-filter-bar">
      <div class="crm-search-input-wrap">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <input type="text" id="inventorySearch" class="crm-search-input" placeholder="Search SKU, Product, Supplier..." oninput="filterInventoryCatalog()">
      </div>
      <select id="categoryFilter" class="crm-select crm-select-sm" onchange="filterInventoryCatalog()">
        <option value="">All Categories</option>
        <option value="PPF">PPF & Vinyl Films</option>
        <option value="COATING">Ceramic & Graphene</option>
        <option value="COMPOUND">Polishing Compounds</option>
        <option value="WASH">Shampoos & Chemicals</option>
        <option value="PADS">Buffing Pads & Towels</option>
        <option value="EQUIPMENT">Studio Tools</option>
      </select>
    </div>
  </div>

  <!-- TAB 1: Product Catalog Table -->
  <div class="crm-tab-pane active" id="paneCatalog">
    <div class="crm-table-container">
      <table class="crm-table" id="inventoryTable">
        <thead>
          <tr>
            <th>SKU & Product Name</th>
            <th>Category</th>
            <th>Stock Level & Meter</th>
            <th>Safety Min</th>
            <th>Unit Cost</th>
            <th>Retail Value</th>
            <th>Location / Supplier</th>
            <th>Quick Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): 
            $stockPct = min(100, round(($item['current_stock'] / max($item['min_stock_alert'] * 3, 1)) * 100));
            $isLow = $item['current_stock'] <= $item['min_stock_alert'];
            $isZero = $item['current_stock'] <= 0;
          ?>
          <tr class="crm-inv-row" data-sku="<?= htmlspecialchars($item['sku']) ?>" data-name="<?= htmlspecialchars($item['name']) ?>" data-cat="<?= htmlspecialchars($item['category']) ?>" data-supplier="<?= htmlspecialchars($item['supplier'] ?? '') ?>">
            <td>
              <div class="crm-item-main">
                <span class="crm-sku-badge"><?= htmlspecialchars($item['sku']) ?></span>
                <div class="crm-item-title"><?= htmlspecialchars($item['name']) ?></div>
              </div>
            </td>
            <td>
              <span class="crm-cat-tag crm-cat-<?= strtolower($item['category']) ?>">
                <?= htmlspecialchars($item['category']) ?>
              </span>
            </td>
            <td>
              <div class="crm-stock-meter-wrap">
                <div class="crm-stock-qty-row">
                  <strong class="crm-qty-num <?= $isLow ? 'crm-qty-low' : '' ?>">
                    <?= number_format((float)$item['current_stock'], 1) ?> <?= htmlspecialchars($item['unit']) ?>
                  </strong>
                  <?php if ($isZero): ?>
                    <span class="crm-pill crm-pill-danger">Out of Stock</span>
                  <?php elseif ($isLow): ?>
                    <span class="crm-pill crm-pill-warning">Low Stock</span>
                  <?php else: ?>
                    <span class="crm-pill crm-pill-success">In Stock</span>
                  <?php endif; ?>
                </div>
                <div class="crm-meter-bar">
                  <div class="crm-meter-fill <?= $isLow ? 'crm-meter-warning' : 'crm-meter-healthy' ?>" style="width: <?= $stockPct ?>%;"></div>
                </div>
              </div>
            </td>
            <td>
              <span class="crm-threshold-val"><?= number_format((float)$item['min_stock_alert'], 1) ?> <?= htmlspecialchars($item['unit']) ?></span>
            </td>
            <td>
              <div class="crm-cost-val">₹<?= number_format((float)$item['unit_cost']) ?></div>
            </td>
            <td>
              <div class="crm-sell-val">₹<?= number_format((float)$item['sell_price']) ?></div>
            </td>
            <td>
              <div class="crm-meta-cell">
                <div class="crm-location-tag">📍 <?= htmlspecialchars($item['location'] ?: 'Studio Shelf') ?></div>
                <div class="crm-supplier-tag"><?= htmlspecialchars($item['supplier'] ?: 'Direct Distributor') ?></div>
              </div>
            </td>
            <td>
              <div class="crm-action-group">
                <button class="crm-btn crm-btn-xs crm-btn-ghost" title="Stock In / Restock" onclick="openStockInModal(<?= $item['id'] ?>)">
                  + Stock In
                </button>
                <button class="crm-btn crm-btn-xs crm-btn-danger-ghost" title="Log Decline / Wastage" onclick="openDeclineStockModal(<?= $item['id'] ?>)">
                  ⚠️ Decline
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- TAB 2: Decline & Wastage Audit Ledger (CRITICAL USER REQUEST) -->
  <div class="crm-tab-pane" id="paneDeclines">
    <div class="crm-card crm-decline-banner-card">
      <div class="crm-decline-banner-inner">
        <div>
          <h3 class="crm-decline-banner-title">⚠️ Decline & Wastage Tracking Ledger</h3>
          <p class="crm-decline-banner-sub">Complete audit trail of written-off detailing products, cutting scrap, transport damage, and expired chemicals.</p>
        </div>
        <button class="crm-btn crm-btn-danger" onclick="openDeclineStockModal()">
          + Log New Decline Incident
        </button>
      </div>
    </div>

    <div class="crm-table-container">
      <table class="crm-table">
        <thead>
          <tr>
            <th>Date & Time</th>
            <th>Item & SKU</th>
            <th>Decline Category</th>
            <th>Quantity Lost</th>
            <th>Unit Cost</th>
            <th>Total Financial Loss</th>
            <th>Incident Reason & Notes</th>
            <th>Logged By</th>
            <th>Ref / Job Card</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($declines)): ?>
            <tr>
              <td colspan="9" style="text-align: center; padding: 30px; color: #8e95a5;">No decline records logged yet.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($declines as $dec): ?>
            <tr>
              <td>
                <div class="crm-time-stamp">
                  <strong><?= date('d M Y', strtotime($dec['created_at'])) ?></strong>
                  <span><?= date('H:i A', strtotime($dec['created_at'])) ?></span>
                </div>
              </td>
              <td>
                <div class="crm-item-main">
                  <span class="crm-sku-badge"><?= htmlspecialchars($dec['sku']) ?></span>
                  <div class="crm-item-title"><?= htmlspecialchars($dec['item_name']) ?></div>
                </div>
              </td>
              <td>
                <?php if ($dec['type'] === 'DECLINE_DAMAGED'): ?>
                  <span class="crm-pill crm-pill-warning">📦 Damaged in Transit</span>
                <?php elseif ($dec['type'] === 'DECLINE_SCRAP'): ?>
                  <span class="crm-pill crm-pill-danger">✂️ Cutting / Install Scrap</span>
                <?php elseif ($dec['type'] === 'DECLINE_EXPIRED'): ?>
                  <span class="crm-pill crm-pill-purple">🧪 Expired / Crystallized</span>
                <?php elseif ($dec['type'] === 'AUDIT_ADJUST'): ?>
                  <span class="crm-pill crm-pill-dark">🔍 Stock Audit Discrepancy</span>
                <?php else: ?>
                  <span class="crm-pill crm-pill-danger"><?= htmlspecialchars($dec['type']) ?></span>
                <?php endif; ?>
              </td>
              <td>
                <strong class="crm-loss-qty"><?= $dec['quantity'] ?> <?= htmlspecialchars($dec['unit']) ?></strong>
              </td>
              <td>
                <span>₹<?= number_format((float)$dec['unit_cost']) ?></span>
              </td>
              <td>
                <span class="crm-loss-amount">₹<?= number_format(abs((float)$dec['total_cost'])) ?></span>
              </td>
              <td>
                <div class="crm-incident-note">
                  <?= htmlspecialchars($dec['reason'] ?? 'No notes provided') ?>
                </div>
              </td>
              <td>
                <div class="crm-logged-by-badge">
                  👤 <?= htmlspecialchars($dec['logged_by'] ?? 'Admin') ?>
                </div>
              </td>
              <td>
                <span class="crm-ref-code"><?= htmlspecialchars($dec['reference_no'] ?: '—') ?></span>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- TAB 3: Full Movement Ledger -->
  <div class="crm-tab-pane" id="paneLedger">
    <div class="crm-table-container">
      <table class="crm-table">
        <thead>
          <tr>
            <th>Date & Time</th>
            <th>Type</th>
            <th>Item & SKU</th>
            <th>Quantity Delta</th>
            <th>Unit Cost</th>
            <th>Total Value</th>
            <th>Reference / Job Card</th>
            <th>Notes & Personnel</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($allTransactions as $tx): 
            $isIncoming = str_starts_with($tx['type'], 'IN_');
            $isDecline = str_starts_with($tx['type'], 'DECLINE_') || $tx['type'] === 'AUDIT_ADJUST';
          ?>
          <tr>
            <td>
              <div class="crm-time-stamp">
                <strong><?= date('d M Y', strtotime($tx['created_at'])) ?></strong>
                <span><?= date('H:i A', strtotime($tx['created_at'])) ?></span>
              </div>
            </td>
            <td>
              <?php if ($isIncoming): ?>
                <span class="crm-pill crm-pill-success">⬇️ Stock In / Purchase</span>
              <?php elseif ($isDecline): ?>
                <span class="crm-pill crm-pill-danger">⚠️ Decline / Scrap</span>
              <?php else: ?>
                <span class="crm-pill crm-pill-info">🚗 Detailing Job Usage</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="crm-item-main">
                <span class="crm-sku-badge"><?= htmlspecialchars($tx['sku']) ?></span>
                <div class="crm-item-title"><?= htmlspecialchars($tx['item_name']) ?></div>
              </div>
            </td>
            <td>
              <strong class="<?= $tx['quantity'] > 0 ? 'crm-text-green' : 'crm-text-red' ?>">
                <?= $tx['quantity'] > 0 ? '+' : '' ?><?= $tx['quantity'] ?> <?= htmlspecialchars($tx['unit']) ?>
              </strong>
            </td>
            <td>₹<?= number_format((float)$tx['unit_cost']) ?></td>
            <td>₹<?= number_format(abs((float)$tx['total_cost'])) ?></td>
            <td><span class="crm-ref-code"><?= htmlspecialchars($tx['reference_no'] ?: '—') ?></span></td>
            <td>
              <div class="crm-incident-note"><?= htmlspecialchars($tx['reason'] ?: '') ?></div>
              <small class="crm-text-muted">By: <?= htmlspecialchars($tx['logged_by'] ?: 'Admin') ?></small>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
