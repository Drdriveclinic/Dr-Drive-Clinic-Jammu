<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Reusable Related Services Component
$relatedServicesList = $relatedServicesList ?? [];
if (empty($relatedServicesList)) return;
?>
<div class="grid-3-col" data-anim="fade-up">
  <?php foreach ($relatedServicesList as $rSvc): ?>
    <div class="wash-trio-card" style="background:var(--white);">
      <div class="wash-trio-img-wrap">
        <div class="img-placeholder" style="height:100%;">
          PHOTO — <?= htmlspecialchars($rSvc['name']) ?>
        </div>
      </div>
      <div class="wash-trio-body">
        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">
            <?= htmlspecialchars(strtoupper($rSvc['category'])) ?>
          </div>
          <h3 class="h3-title" style="font-size:26px; margin-bottom:12px;">
            <a href="/services/<?= htmlspecialchars($rSvc['slug']) ?>"><?= htmlspecialchars($rSvc['name']) ?></a>
          </h3>
          <p style="color:var(--text-2); font-size:14px; line-height:1.6; margin-bottom:24px;">
            <?= htmlspecialchars($rSvc['short_desc']) ?>
          </p>
        </div>
        <a href="/services/<?= htmlspecialchars($rSvc['slug']) ?>" class="text-link">VIEW SERVICE →</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>
