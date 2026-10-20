<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Job Cards & Workshop Bays
$jobCards = Database::query("
  SELECT j.*, 
         COALESCE(j.technician_notes, '') as tech_notes,
         c.name as customer_name, c.phone as customer_phone, 
         v.brand, v.model, v.registration, v.color
  FROM job_cards j
  LEFT JOIN customers c ON j.customer_id = c.id
  LEFT JOIN vehicles v ON j.vehicle_id = v.id
  ORDER BY j.created_at DESC
");

$staff = Database::query("SELECT id, name, role FROM staff WHERE status = 'ACTIVE' ORDER BY name ASC");
$activeJobs = array_filter($jobCards, fn($j) => ($j['status'] ?? '') === 'IN_PROGRESS');
?>
<div class="crm-jobcards-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">OPERATIONS &bull; WORKSHOP FLOOR</div>
      <h1 class="crm-page-heading">Live Job Cards & Digital Bay Board</h1>
      <p class="crm-page-sub">Track active bay work, pre-service vehicle inspection, paint defects, custom services & product pricing, and 1-click tax invoicing.</p>
    </div>
    <div class="crm-page-actions">
      <button class="crm-btn crm-btn-primary" onclick="openCreateJobCardModal()">+ New Studio Job Card</button>
    </div>
  </div>

  <!-- Workshop Bay Grid -->
  <div class="crm-bay-status-cards">
    <?php
    $bayConfigs = [
      1 => ['name' => 'BAY 01 &bull; DUST-FREE CLEANROOM', 'type' => 'PPF & Cleanroom'],
      2 => ['name' => 'BAY 02 &bull; PAINT CORRECTION', 'type' => 'Ceramic & Correction'],
      3 => ['name' => 'BAY 03 &bull; DECON & WASH', 'type' => 'Steam & Foam Decon'],
      4 => ['name' => 'BAY 04 &bull; PPF FINISHING', 'type' => 'Film Wrap & Finishing']
    ];

    for ($b = 1; $b <= 4; $b++):
      $bayJob = null;
      foreach ($activeJobs as $aj) {
        if ((int)($aj['bay_no'] ?? 0) === $b) {
          $bayJob = $aj;
          break;
        }
      }
    ?>
      <?php if ($bayJob): ?>
        <div class="crm-bay-card crm-bay-active">
          <div class="crm-bay-card-header">
            <span class="crm-bay-badge"><?= $bayConfigs[$b]['name'] ?></span>
            <span class="crm-pill crm-pill-progress">In Progress</span>
          </div>
          <div class="crm-bay-car-title"><?= htmlspecialchars(($bayJob['brand'] ?? '') . ' ' . ($bayJob['model'] ?? 'Car')) ?></div>
          <div class="crm-bay-reg-plate"><?= htmlspecialchars($bayJob['registration'] ?: 'Pending Reg') ?></div>
          <div class="crm-bay-details">
            <div class="crm-bay-detail-row">
              <span>Customer:</span>
              <strong><?= htmlspecialchars($bayJob['customer_name'] ?? 'Client') ?></strong>
            </div>
            <div class="crm-bay-detail-row">
              <span>Custom Services:</span>
              <strong style="color:#fff;"><?= htmlspecialchars($bayJob['service_names'] ?? 'Detailing') ?></strong>
            </div>
            <div class="crm-bay-detail-row">
              <span>Lead Tech:</span>
              <strong><?= htmlspecialchars($bayJob['assigned_tech'] ?? 'Master Tech') ?></strong>
            </div>
            <div class="crm-bay-detail-row">
              <span>Billed Value:</span>
              <strong class="crm-text-amber">₹<?= number_format((float)$bayJob['total_amount']) ?></strong>
            </div>
          </div>
          <div class="crm-bay-footer">
            <span class="crm-card-no">#<?= htmlspecialchars($bayJob['card_no']) ?></span>
            <div style="display:flex; gap:6px;">
              <button class="crm-btn crm-btn-xs crm-btn-ghost" onclick="inspectJobCard(<?= htmlspecialchars(json_encode($bayJob)) ?>)">Inspect</button>
              <button class="crm-btn crm-btn-xs crm-btn-success" onclick="markJobCompleted(<?= $bayJob['id'] ?>)">✓ Complete</button>
            </div>
          </div>
        </div>
      <?php else: ?>
        <div class="crm-bay-card crm-bay-idle">
          <div class="crm-bay-card-header">
            <span class="crm-bay-badge"><?= $bayConfigs[$b]['name'] ?></span>
            <span class="crm-pill crm-pill-success">Available</span>
          </div>
          <div class="crm-bay-empty-state">
            <div class="crm-empty-icon">🚗</div>
            <h4>Bay Cleaned & Calibrated</h4>
            <p>Ready for next vehicle arrival</p>
          </div>
          <div class="crm-bay-footer">
            <button class="crm-btn crm-btn-sm crm-btn-primary" onclick="openCreateJobCardModal(<?= $b ?>)">Assign to Bay 0<?= $b ?></button>
          </div>
        </div>
      <?php endif; ?>
    <?php endfor; ?>
  </div>

  <!-- Job Cards Table -->
  <div class="crm-card">
    <div class="crm-card-header">
      <h2 class="crm-card-title">All Studio Job Cards</h2>
      <span class="crm-text-muted"><?= count($jobCards) ?> Active Records</span>
    </div>

    <div class="crm-table-container">
      <table class="crm-table">
        <thead>
          <tr>
            <th>Job Card #</th>
            <th>Customer & Contact</th>
            <th>Vehicle Details</th>
            <th>Services & Custom Items</th>
            <th>Assigned Tech</th>
            <th>Bay</th>
            <th>Status</th>
            <th>Total Price</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($jobCards)): ?>
            <?php foreach ($jobCards as $jc): ?>
            <tr>
              <td>
                <strong class="crm-job-num"><?= htmlspecialchars($jc['card_no']) ?></strong>
                <div class="crm-text-muted" style="font-size:11px;"><?= date('d M Y', strtotime($jc['created_at'])) ?></div>
              </td>
              <td>
                <strong><?= htmlspecialchars($jc['customer_name'] ?? 'Walk-in') ?></strong>
                <div class="crm-text-muted"><?= htmlspecialchars($jc['customer_phone'] ?? '—') ?></div>
              </td>
              <td>
                <strong><?= htmlspecialchars(($jc['brand'] ?? '') . ' ' . ($jc['model'] ?? '')) ?></strong>
                <div class="crm-plate-tag"><?= htmlspecialchars($jc['registration'] ?: 'Pending Reg') ?></div>
              </td>
              <td>
                <div class="crm-service-tag" style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= htmlspecialchars($jc['service_names']) ?>">
                  <?= htmlspecialchars($jc['service_names']) ?>
                </div>
              </td>
              <td>
                <div class="crm-tech-pill">
                  <span>👤</span> <?= htmlspecialchars($jc['assigned_tech'] ?: 'Unassigned') ?>
                </div>
              </td>
              <td>
                <span class="crm-source-badge">Bay 0<?= $jc['bay_no'] ?></span>
              </td>
              <td>
                <?php if ($jc['status'] === 'IN_PROGRESS'): ?>
                  <span class="crm-pill crm-pill-progress">In Progress</span>
                <?php elseif ($jc['status'] === 'COMPLETED'): ?>
                  <span class="crm-pill crm-pill-success">Completed</span>
                <?php else: ?>
                  <span class="crm-pill crm-pill-confirmed"><?= htmlspecialchars($jc['status']) ?></span>
                <?php endif; ?>
              </td>
              <td>
                <strong class="crm-amount-val" style="color:#fff;">₹<?= number_format((float)$jc['total_amount']) ?></strong>
              </td>
              <td style="text-align:right;">
                <div class="crm-action-group" style="justify-content:flex-end; gap:6px;">
                  <button class="crm-btn crm-btn-xs crm-btn-ghost" onclick="inspectJobCard(<?= htmlspecialchars(json_encode($jc)) ?>)">
                    Checklist
                  </button>
                  <button class="crm-btn crm-btn-xs crm-btn-primary" onclick="convertJobCardToInvoice(<?= htmlspecialchars(json_encode($jc)) ?>)">
                    🧾 Invoice
                  </button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" style="text-align:center; padding:44px 20px; color:#888888;">
                <div style="font-size:32px; margin-bottom:10px;">📋</div>
                <strong style="color:#ffffff; font-size:14px;">No Active Bay Job Cards</strong>
                <p style="font-size:12px; margin:6px 0 0; color:#666666;">Click "+ New Studio Job Card" above to initiate a bay inspection card.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal: Create New Job Card with Custom Items & Custom Price -->
<div class="crm-modal-backdrop" id="modalCreateJobCard">
  <div class="crm-modal crm-modal-lg" style="max-width:820px;">
    <div class="crm-modal-header">
      <div class="crm-modal-title-group">
        <h3 class="crm-modal-title">Initiate Workshop Job Card</h3>
        <p class="crm-modal-sub">Log vehicle check-in, assign detailing bay, and add custom services with custom pricing.</p>
      </div>
      <button class="crm-modal-close" onclick="closeModal('modalCreateJobCard')">&times;</button>
    </div>
    <form id="formCreateJobCard" onsubmit="handleCreateJobCardSubmit(event)">
      <div class="crm-modal-body">
        
        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Customer Name *</label>
            <input type="text" name="customer_name" class="crm-input" placeholder="e.g. Rahul Sharma" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Customer Phone *</label>
            <input type="tel" name="customer_phone" class="crm-input" placeholder="+91 94191 XXXXX" required>
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Vehicle Brand & Model *</label>
            <input type="text" name="vehicle_model" class="crm-input" placeholder="e.g. Mahindra Thar Roxx" required>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Registration Number *</label>
            <input type="text" name="vehicle_reg" class="crm-input" placeholder="e.g. JK 02 DB 0001" required style="text-transform:uppercase;">
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Workshop Bay *</label>
            <select name="bay_no" id="jcBaySelect" class="crm-select" required>
              <option value="1">Bay 01 — Dust-Free PPF Cleanroom</option>
              <option value="2">Bay 02 — Paint Correction & Ceramic</option>
              <option value="3">Bay 03 — Precision Decon & Foam Wash</option>
              <option value="4">Bay 04 — PPF Wrapping & Final Audit</option>
            </select>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Assigned Lead Technician *</label>
            <select name="assigned_tech" class="crm-select" required>
              <?php foreach ($staff as $st): ?>
                <option value="<?= htmlspecialchars($st['name']) ?>"><?= htmlspecialchars($st['name']) ?> (<?= htmlspecialchars($st['role']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Custom Line Items Table in Job Card -->
        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:8px; padding:16px; margin-bottom:16px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <span style="font-size:11px; font-weight:800; color:var(--red); letter-spacing:0.12em; text-transform:uppercase;">ASSIGNED CUSTOM SERVICES & PRODUCTS</span>
            <button type="button" class="crm-btn crm-btn-xs crm-btn-secondary" onclick="addJcCustomItem()">+ Add Custom Line Item</button>
          </div>

          <table style="width:100%; border-collapse:collapse; font-size:12px;" id="jcItemsTable">
            <thead>
              <tr style="border-bottom:1px solid rgba(255,255,255,0.1); color:#94A3B8; font-size:10px; text-transform:uppercase;">
                <th style="padding:6px; text-align:left;">Custom Service / Product Name</th>
                <th style="padding:6px; width:90px; text-align:center;">Type</th>
                <th style="padding:6px; width:130px; text-align:right;">Custom Price (₹)</th>
                <th style="padding:6px; width:40px;"></th>
              </tr>
            </thead>
            <tbody id="jcItemsBody">
              <tr>
                <td style="padding:6px;">
                  <input type="text" class="crm-input jc-item-name" value="STEK DYNOshield PPF High Impact Frontal" placeholder="Custom service name" required style="font-size:12px; padding:6px 10px;">
                </td>
                <td style="padding:6px; text-align:center;">
                  <select class="crm-select jc-item-type" style="font-size:11px; padding:4px;">
                    <option value="service" selected>Service</option>
                    <option value="product">Product</option>
                  </select>
                </td>
                <td style="padding:6px; text-align:right;">
                  <input type="number" step="100" class="crm-input jc-item-price" value="28000" style="font-size:12px; padding:6px 10px; text-align:right;" oninput="recalcJcTotal()">
                </td>
                <td style="padding:6px; text-align:center;">
                  <button type="button" class="crm-btn crm-btn-xs crm-btn-danger" onclick="removeJcItem(this)">✕</button>
                </td>
              </tr>
            </tbody>
          </table>

          <div style="text-align:right; margin-top:10px; font-size:14px; font-weight:800; color:#fff;">
            Total Estimated Amount: <span style="color:#10b981;" id="jcTotalDisplay">₹28,000</span>
            <input type="hidden" name="total_amount" id="jcTotalAmount" value="28000">
          </div>
        </div>

        <div class="crm-form-row">
          <div class="crm-form-group">
            <label class="crm-label">Fuel Level</label>
            <select name="fuel_level" class="crm-select">
              <option value="1/4 Tank">1/4 Tank</option>
              <option value="1/2 Tank" selected>1/2 Tank</option>
              <option value="3/4 Tank">3/4 Tank</option>
              <option value="Full Tank">Full Tank</option>
            </select>
          </div>
          <div class="crm-form-group">
            <label class="crm-label">Odometer Reading (KM)</label>
            <input type="text" name="odometer" class="crm-input" placeholder="e.g. 14,250 KM">
          </div>
        </div>

        <div class="crm-form-group">
          <label class="crm-label">Pre-Service Vehicle Defects / Scratches / Inspection Notes</label>
          <textarea name="technician_notes" class="crm-textarea" placeholder="e.g. Minor swirl marks on hood; scratch on left rear fender; customer approved front PPF wrap."></textarea>
        </div>
      </div>
      <div class="crm-modal-footer">
        <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalCreateJobCard')">Cancel</button>
        <button type="submit" class="crm-btn crm-btn-primary">Dispatch Vehicle to Bay</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Digital Inspection Checklist Viewer -->
<div class="crm-modal-backdrop" id="modalInspectJobCard">
  <div class="crm-modal crm-modal-md">
    <div class="crm-modal-header">
      <div class="crm-modal-title-group">
        <h3 class="crm-modal-title" id="inspectCardTitle">Job Card Inspection Details</h3>
        <p class="crm-modal-sub" id="inspectCardSub">Workshop bay check-in log and pre-existing defect record.</p>
      </div>
      <button class="crm-modal-close" onclick="closeModal('modalInspectJobCard')">&times;</button>
    </div>
    <div class="crm-modal-body" id="inspectCardBody">
      <!-- Injected dynamically -->
    </div>
    <div class="crm-modal-footer">
      <button type="button" class="crm-btn crm-btn-secondary" onclick="closeModal('modalInspectJobCard')">Close</button>
      <button type="button" class="crm-btn crm-btn-primary" id="inspectInvoiceBtn">Generate Tax Invoice</button>
    </div>
  </div>
</div>

<script>
function openCreateJobCardModal(bay = 1) {
  openModal('modalCreateJobCard');
  if (bay) document.getElementById('jcBaySelect').value = bay;
  recalcJcTotal();
}

function addJcCustomItem(name = '', price = 0) {
  const tbody = document.getElementById('jcItemsBody');
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td style="padding:6px;">
      <input type="text" class="crm-input jc-item-name" value="${name}" placeholder="Custom service or product" required style="font-size:12px; padding:6px 10px;">
    </td>
    <td style="padding:6px; text-align:center;">
      <select class="crm-select jc-item-type" style="font-size:11px; padding:4px;">
        <option value="service" selected>Service</option>
        <option value="product">Product</option>
      </select>
    </td>
    <td style="padding:6px; text-align:right;">
      <input type="number" step="100" class="crm-input jc-item-price" value="${price}" style="font-size:12px; padding:6px 10px; text-align:right;" oninput="recalcJcTotal()">
    </td>
    <td style="padding:6px; text-align:center;">
      <button type="button" class="crm-btn crm-btn-xs crm-btn-danger" onclick="removeJcItem(this)">✕</button>
    </td>
  `;
  tbody.appendChild(tr);
  recalcJcTotal();
}

function removeJcItem(btn) {
  const tbody = document.getElementById('jcItemsBody');
  if (tbody.querySelectorAll('tr').length > 1) {
    btn.closest('tr').remove();
    recalcJcTotal();
  }
}

function recalcJcTotal() {
  const rows = document.querySelectorAll('#jcItemsBody tr');
  let total = 0;
  rows.forEach(r => {
    total += parseFloat(r.querySelector('.jc-item-price').value || 0);
  });
  document.getElementById('jcTotalDisplay').textContent = '₹' + total.toLocaleString();
  document.getElementById('jcTotalAmount').value = total;
}

async function handleCreateJobCardSubmit(e) {
  e.preventDefault();
  const form = e.target;
  const rows = document.querySelectorAll('#jcItemsBody tr');
  const items = [];
  const names = [];

  rows.forEach(r => {
    const n = r.querySelector('.jc-item-name').value.trim();
    const t = r.querySelector('.jc-item-type').value;
    const p = parseFloat(r.querySelector('.jc-item-price').value || 0);
    items.push({ name: n, type: t, qty: 1, rate: p, total: p });
    names.push(n);
  });

  const payload = {
    customer_name: form.customer_name.value.trim(),
    customer_phone: form.customer_phone.value.trim(),
    vehicle_model: form.vehicle_model.value.trim(),
    vehicle_reg: form.vehicle_reg.value.trim(),
    bay_no: parseInt(form.bay_no.value, 10),
    assigned_tech: form.assigned_tech.value,
    service_names: names.join(' + '),
    items_json: JSON.stringify(items),
    total_amount: parseFloat(document.getElementById('jcTotalAmount').value || 0),
    fuel_level: form.fuel_level.value,
    odometer: form.odometer.value.trim(),
    technician_notes: form.technician_notes.value.trim()
  };

  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Dispatching to Bay...';

  try {
    const res = await fetch('/crm/api/job-cards/create', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (data.success) {
      showToast('Job card #' + (data.card_no || '') + ' initiated in Bay 0' + payload.bay_no + '!', 'success');
      closeModal('modalCreateJobCard');
      setTimeout(() => location.reload(), 600);
    } else {
      showToast(data.error || 'Failed to create job card', 'danger');
    }
  } catch (err) {
    showToast('Network error creating job card', 'danger');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Dispatch Vehicle to Bay';
  }
}

function inspectJobCard(jc) {
  document.getElementById('inspectCardTitle').textContent = 'Job Card #' + (jc.card_no || 'Details');
  document.getElementById('inspectCardSub').textContent = (jc.brand || '') + ' ' + (jc.model || '') + ' (' + (jc.registration || 'Pending') + ')';
  
  let html = `
    <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:6px; padding:14px; margin-bottom:16px;">
      <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
        <span>Customer:</span>
        <strong style="color:#fff;">${jc.customer_name || 'Client'} (${jc.customer_phone || '—'})</strong>
      </div>
      <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
        <span>Assigned Bay:</span>
        <strong style="color:var(--red);">Bay 0${jc.bay_no}</strong>
      </div>
      <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
        <span>Lead Technician:</span>
        <strong style="color:#fff;">${jc.assigned_tech || 'Studio Lead'}</strong>
      </div>
      <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
        <span>Odometer / Fuel:</span>
        <span style="color:#CBD5E1;">${jc.odometer || 'N/A'} &bull; ${jc.fuel_level || '1/2 Tank'}</span>
      </div>
    </div>

    <div style="margin-bottom:16px;">
      <span style="font-size:11px; font-weight:800; color:var(--red); text-transform:uppercase;">SERVICES TO EXECUTE:</span>
      <div style="font-size:15px; font-weight:700; color:#fff; margin-top:4px;">${jc.service_names || 'Standard Detailing'}</div>
      <div style="color:#10b981; font-weight:700; margin-top:2px;">Billed Estimate: ₹${(parseFloat(jc.total_amount) || 0).toLocaleString()}</div>
    </div>

    <div style="background:#0F1420; border-radius:6px; padding:12px; margin-bottom:12px;">
      <span style="font-size:10px; font-weight:800; color:#94A3B8; text-transform:uppercase;">PRE-INSPECTION SCRATCHES & DEFECT NOTES:</span>
      <p style="font-size:13px; color:#E2E8F0; margin-top:4px;">${jc.technician_notes || 'No pre-existing scratches reported by client. Vehicle clean.'}</p>
    </div>
  `;

  document.getElementById('inspectCardBody').innerHTML = html;
  document.getElementById('inspectInvoiceBtn').onclick = () => convertJobCardToInvoice(jc);
  openModal('modalInspectJobCard');
}

function convertJobCardToInvoice(jc) {
  closeModal('modalInspectJobCard');
  openCreateInvoiceModal();
  document.getElementById('invCustName').value = jc.customer_name || '';
  document.getElementById('invCustPhone').value = jc.customer_phone || '';
  document.getElementById('invVehModel').value = (jc.brand ? jc.brand + ' ' : '') + (jc.model || '');
  document.getElementById('invVehReg').value = jc.registration || '';

  // Clear items and add job card service
  const tbody = document.getElementById('invItemsBody');
  tbody.innerHTML = '';
  addCustomLineItem('service', jc.service_names || 'Studio Automotive Detailing', parseFloat(jc.total_amount || 0));
  recalcInvoiceTotals();
}

async function markJobCompleted(id) {
  if (!confirm('Mark this Bay Job Card as COMPLETED and ready for customer handover?')) return;
  try {
    const res = await fetch('/crm/api/job-cards/status', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id, status: 'COMPLETED' })
    });
    const d = await res.json();
    if (d.success) {
      showToast('Job card completed!', 'success');
      setTimeout(() => location.reload(), 600);
    }
  } catch (err) {
    showToast('Error updating job card', 'danger');
  }
}
</script>
