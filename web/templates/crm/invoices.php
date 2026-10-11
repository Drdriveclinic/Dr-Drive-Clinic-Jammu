<?php
// THE DRIVE CLINIC — Invoices & Billing
$invoices = Database::query("
  SELECT i.*, c.name as customer_name, c.phone as customer_phone,
         v.brand, v.model, v.registration
  FROM invoices i
  LEFT JOIN customers c ON i.customer_id = c.id
  LEFT JOIN vehicles v ON i.vehicle_id = v.id
  ORDER BY i.created_at DESC
");
?>
<div class="crm-invoices-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">BUSINESS &bull; BILLING & GST</div>
      <h1 class="crm-page-heading">Quotations & Tax Invoices</h1>
      <p class="crm-page-sub">GST-compliant automotive invoices, payment status tracking, and printable tax receipts.</p>
    </div>
    <div class="crm-page-actions">
      <button class="crm-btn crm-btn-primary" onclick="alert('Creating new studio tax invoice')">+ Create Invoice</button>
    </div>
  </div>

  <div class="crm-card">
    <div class="crm-table-container">
      <table class="crm-table">
        <thead>
          <tr>
            <th>Invoice #</th>
            <th>Date</th>
            <th>Customer & Vehicle</th>
            <th>Subtotal</th>
            <th>GST (18%)</th>
            <th>Grand Total</th>
            <th>Status</th>
            <th>Payment Method</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($invoices)): ?>
            <?php foreach ($invoices as $inv): ?>
            <tr>
              <td><strong class="crm-ref-code"><?= htmlspecialchars($inv['invoice_no']) ?></strong></td>
              <td><?= date('d M Y', strtotime($inv['created_at'])) ?></td>
              <td>
                <strong><?= htmlspecialchars($inv['customer_name'] ?? 'Walk-in') ?></strong>
                <div class="crm-text-muted" style="font-size:11px;"><?= htmlspecialchars(($inv['brand'] ?? '') . ' ' . ($inv['model'] ?? '')) ?> (<?= htmlspecialchars($inv['registration'] ?? '') ?>)</div>
              </td>
              <td>₹<?= number_format((float)$inv['subtotal']) ?></td>
              <td>₹<?= number_format((float)$inv['gst_amount']) ?></td>
              <td><strong class="crm-amount-val">₹<?= number_format((float)$inv['grand_total']) ?></strong></td>
              <td>
                <?php if ($inv['payment_status'] === 'PAID'): ?>
                  <span class="crm-pill crm-pill-success">Paid</span>
                <?php else: ?>
                  <span class="crm-pill crm-pill-warning">Pending (₹<?= number_format((float)$inv['grand_total']) ?>)</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="crm-source-badge"><?= htmlspecialchars($inv['payment_method'] ?: 'Pending') ?></span>
              </td>
              <td>
                <div class="crm-action-group">
                  <?php if ($inv['payment_status'] !== 'PAID'): ?>
                    <button class="crm-btn crm-btn-xs crm-btn-success" onclick="markInvoicePaid(<?= $inv['id'] ?>)">
                      Mark Paid
                    </button>
                  <?php endif; ?>
                  <button class="crm-btn crm-btn-xs crm-btn-ghost" onclick="alert('Print invoice #<?= htmlspecialchars($inv['invoice_no']) ?>')">
                    🖨️ Print
                  </button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" style="text-align:center; padding:44px 20px; color:#888888;">
                <div style="font-size:32px; margin-bottom:10px;">🧾</div>
                <strong style="color:#ffffff; font-size:14px;">No Invoices Generated Yet</strong>
                <p style="font-size:12px; margin:6px 0 0; color:#666666;">Completed job cards and billings will generate GST tax invoices here.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
