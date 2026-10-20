<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Executive Revenue CRM & Financial Intelligence
$invoices = Database::query("SELECT * FROM invoices ORDER BY created_at DESC");
$expenses = Database::query("SELECT * FROM expenses ORDER BY date DESC");
$memberships = Database::query("SELECT * FROM memberships WHERE status = 'ACTIVE'");
$jobCards = Database::query("SELECT * FROM job_cards");

$grossInvoiced = 0.0;
$collectedCash = 0.0;
$pendingReceivables = 0.0;
$totalGst = 0.0;

foreach ($invoices as $inv) {
    $grand = (float)$inv['grand_total'];
    $paid = (float)($inv['paid_amount'] ?: ($inv['payment_status'] === 'PAID' ? $grand : 0));
    $grossInvoiced += $grand;
    $collectedCash += $paid;
    $pendingReceivables += max(0, $grand - $paid);
    $totalGst += (float)$inv['gst_amount'];
}

$totalExpenses = 0.0;
foreach ($expenses as $ex) {
    $totalExpenses += (float)$ex['amount'];
}

$membershipMRR = 0.0;
foreach ($memberships as $m) {
    $membershipMRR += ($m['billing_cycle'] === 'Monthly' ? (float)$m['price'] : ((float)$m['price'] / 12));
}

// Financial bottom line
$netOperatingProfit = $collectedCash - $totalExpenses;
$operatingMargin = $collectedCash > 0 ? round(($netOperatingProfit / $collectedCash) * 100, 1) : 0;
$totalJobsCount = count($jobCards) ?: count($invoices);
$avgTicket = $totalJobsCount > 0 ? round($grossInvoiced / $totalJobsCount) : 0;
?>
<div class="crm-analytics-page">
  <div class="crm-page-header-row">
    <div>
      <div class="crm-kicker">FINANCIAL INTELLIGENCE &bull; EXECUTIVE SUITE</div>
      <h1 class="crm-page-heading">Revenue CRM & Profit Ledger</h1>
      <p class="crm-page-sub">Real-time financial telemetry tracking gross invoiced revenue, digital collections, workshop operating expenses, recurring memberships, and studio net margin.</p>
    </div>
    <div class="crm-page-actions" style="display:flex; gap:10px;">
      <a href="/crm/invoices" class="crm-btn crm-btn-secondary">View Tax Invoices</a>
      <a href="/crm/payments" class="crm-btn crm-btn-primary">Open Expense Ledger</a>
    </div>
  </div>

  <!-- Primary Financial Metrics Grid -->
  <div class="crm-kpi-grid">
    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Gross Invoiced Revenue</span>
        <span class="crm-kpi-icon" style="color:#38bdf8;">📈</span>
      </div>
      <div class="crm-kpi-value">₹<?= number_format($grossInvoiced) ?></div>
      <div class="crm-kpi-change positive"><?= count($invoices) ?> Total Invoices Billed</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Realized Collections</span>
        <span class="crm-kpi-icon" style="color:#10b981;">💰</span>
      </div>
      <div class="crm-kpi-value" style="color:#10b981;">₹<?= number_format($collectedCash) ?></div>
      <div class="crm-kpi-change positive">Settled in Bank / UPI / Cash</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Workshop Operating Costs</span>
        <span class="crm-kpi-icon" style="color:#ef4444;">📉</span>
      </div>
      <div class="crm-kpi-value" style="color:#ef4444;">₹<?= number_format($totalExpenses) ?></div>
      <div class="crm-kpi-change negative">Rent, Rolls, Power & Consumables</div>
    </div>

    <div class="crm-kpi-card">
      <div class="crm-kpi-header">
        <span class="crm-kpi-title">Net Studio Operating Profit</span>
        <span class="crm-kpi-icon" style="color:#f59e0b;">💎</span>
      </div>
      <div class="crm-kpi-value" style="color:<?= $netOperatingProfit >= 0 ? '#10b981' : '#f43f5e' ?>;">
        ₹<?= number_format($netOperatingProfit) ?>
      </div>
      <div class="crm-kpi-change <?= $operatingMargin >= 0 ? 'positive' : 'negative' ?>">
        <?= $operatingMargin ?>% Net Operating Margin
      </div>
    </div>
  </div>

  <!-- Secondary Metrics: MRR, Average Ticket, Pending Receivables -->
  <div class="crm-stats-grid" style="margin-top:24px;">
    <div class="crm-stat-card">
      <div class="crm-stat-label">Recurring Membership MRR</div>
      <div class="crm-stat-value" style="color:#10b981;">₹<?= number_format(round($membershipMRR)) ?> / mo</div>
      <div class="crm-stat-footer"><?= count($memberships) ?> Active VIP Wash Pass Holders</div>
    </div>

    <div class="crm-stat-card">
      <div class="crm-stat-label">Average Ticket Size (ARPV)</div>
      <div class="crm-stat-value">₹<?= number_format($avgTicket) ?></div>
      <div class="crm-stat-footer">Average bill per vehicle arrival</div>
    </div>

    <div class="crm-stat-card">
      <div class="crm-stat-label">Pending Client Receivables</div>
      <div class="crm-stat-value" style="color:#f59e0b;">₹<?= number_format($pendingReceivables) ?></div>
      <div class="crm-stat-footer">Unsettled invoice balances</div>
    </div>

    <div class="crm-stat-card">
      <div class="crm-stat-label">GST Tax Collected (18%)</div>
      <div class="crm-stat-value" style="color:#c084fc;">₹<?= number_format($totalGst) ?></div>
      <div class="crm-stat-footer">Input tax credit & GST liability</div>
    </div>
  </div>

  <!-- Service Streams Revenue & Margin Performance Table -->
  <div class="crm-card" style="margin-top:28px;">
    <div class="crm-card-header" style="border-bottom:1px solid var(--crm-border); padding:16px 20px;">
      <h2 class="crm-card-title">Service Streams Profitability & Volume</h2>
      <span class="crm-text-muted">Breakdown of gross revenue, consumables cost, and gross profit margin</span>
    </div>
    <div class="crm-table-container">
      <table class="crm-table">
        <thead>
          <tr>
            <th>Service Stream / Studio Category</th>
            <th>Jobs Completed</th>
            <th>Gross Billed Value</th>
            <th>Estimated Consumables Cost</th>
            <th>Net Gross Profit</th>
            <th>Gross Margin</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:16px;">🛡️</span>
                <div>
                  <strong>Paint Protection Film (STEK & Garware PPF)</strong>
                  <div class="crm-text-muted" style="font-size:11px;">High impact stone chip self-healing TPU armor</div>
                </div>
              </div>
            </td>
            <td>8 Vehicles</td>
            <td><strong>₹5,80,000</strong></td>
            <td>₹2,32,000</td>
            <td><strong style="color:#10b981;">₹3,48,000</strong></td>
            <td><span class="crm-pill crm-pill-success">60.0% Margin</span></td>
          </tr>
          <tr>
            <td>
              <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:16px;">✨</span>
                <div>
                  <strong>Ceramic & Graphene Coating 9H+ (Gtechniq)</strong>
                  <div class="crm-text-muted" style="font-size:11px;">Nanotechnology liquid quartz mirror candy gloss</div>
                </div>
              </div>
            </td>
            <td>16 Vehicles</td>
            <td><strong>₹3,84,000</strong></td>
            <td>₹53,760</td>
            <td><strong style="color:#10b981;">₹3,30,240</strong></td>
            <td><span class="crm-pill crm-pill-success">86.0% Margin</span></td>
          </tr>
          <tr>
            <td>
              <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:16px;">🏍️</span>
                <div>
                  <strong>Superbike Precision Detailing & Matte Care</strong>
                  <div class="crm-text-muted" style="font-size:11px;">Two-wheeler deep engine decon, chain & tank polish</div>
                </div>
              </div>
            </td>
            <td>12 Bikes</td>
            <td><strong>₹1,02,000</strong></td>
            <td>₹14,280</td>
            <td><strong style="color:#10b981;">₹87,720</strong></td>
            <td><span class="crm-pill crm-pill-success">86.0% Margin</span></td>
          </tr>
          <tr>
            <td>
              <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:16px;">🚿</span>
                <div>
                  <strong>Signature Ceramic Wash & Deep Steam Decon</strong>
                  <div class="crm-text-muted" style="font-size:11px;">SiO2 foam bath, wheels decon & interior steam sanitization</div>
                </div>
              </div>
            </td>
            <td>52 Washes</td>
            <td><strong>₹1,04,000</strong></td>
            <td>₹12,480</td>
            <td><strong style="color:#10b981;">₹91,520</strong></td>
            <td><span class="crm-pill crm-pill-success">88.0% Margin</span></td>
          </tr>
          <tr>
            <td>
              <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:16px;">⭐</span>
                <div>
                  <strong>VIP Club Subscriptions & Detailing Passes</strong>
                  <div class="crm-text-muted" style="font-size:11px;">Monthly wash passes and ceramic annual maintenance plans</div>
                </div>
              </div>
            </td>
            <td><?= count($memberships) ?> Members</td>
            <td><strong>₹<?= number_format(round($membershipMRR * 12)) ?></strong></td>
            <td>₹<?= number_format(round($membershipMRR * 12 * 0.15)) ?></td>
            <td><strong style="color:#10b981;">₹<?= number_format(round($membershipMRR * 12 * 0.85)) ?></strong></td>
            <td><span class="crm-pill crm-pill-success">85.0% Margin</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
