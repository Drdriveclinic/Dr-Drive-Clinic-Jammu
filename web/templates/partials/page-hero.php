<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Reusable PageHero Component
$heroEyebrow = $heroEyebrow ?? 'THE DRIVE CLINIC';
$heroTitle = $heroTitle ?? 'SERVICE';
$heroLead = $heroLead ?? '';
$heroBreadcrumb = $heroBreadcrumb ?? [];
$heroSlidesList = $heroSlidesList ?? [];
$primaryCtaText = $primaryCtaText ?? 'BOOK THIS SERVICE →';
$primaryCtaHref = $primaryCtaHref ?? '/book';
$secondaryCtaText = $secondaryCtaText ?? 'EXPLORE ALL SERVICES →';
$secondaryCtaHref = $secondaryCtaHref ?? '/services';
?>
<section class="section section-dark page-hero" style="padding-top: 170px; padding-bottom: 90px; position:relative; overflow:hidden;">
  <div class="container">
    <!-- Breadcrumb Navigation -->
    <?php if (!empty($heroBreadcrumb)): ?>
      <nav class="breadcrumb-nav" aria-label="Breadcrumbs" style="margin-bottom: 24px;">
        <ol style="display:flex; align-items:center; gap:10px; font-size:12px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:#9ca3af;">
          <li><a href="/" style="color:#d1d5db; transition:color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#d1d5db'">HOME</a></li>
          <?php foreach ($heroBreadcrumb as $item): ?>
            <li style="color:var(--red);">/</li>
            <?php if (!empty($item['url'])): ?>
              <li><a href="<?= htmlspecialchars($item['url']) ?>" style="color:#d1d5db; transition:color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#d1d5db'"><?= htmlspecialchars($item['label']) ?></a></li>
            <?php else: ?>
              <li style="color:var(--white);" aria-current="page"><?= htmlspecialchars($item['label']) ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ol>
      </nav>
    <?php endif; ?>

    <div class="grid-5-7" style="align-items:flex-end;">
      <div>
        <div class="eyebrow" data-anim="fade-up"><?= htmlspecialchars($heroEyebrow) ?></div>
        
        <h1 class="h1-hero" style="color:var(--white); margin-bottom: 24px;" data-anim="text-reveal">
          <?= htmlspecialchars($heroTitle) ?>
        </h1>

        <div class="redline" style="max-width: 140px; margin-bottom: 28px;" data-anim="redline"></div>

        <?php if (!empty($heroLead)): ?>
          <p class="lead-text" style="color:#d1d5db; max-width: 600px; margin-bottom: 36px; line-height:1.65;" data-anim="fade-up">
            <?= htmlspecialchars($heroLead) ?>
          </p>
        <?php endif; ?>

        <div style="display:flex; gap:16px; flex-wrap:wrap;" data-anim="fade-up">
          <a href="<?= htmlspecialchars($primaryCtaHref) ?>" class="btn btn-red" data-cursor="BOOK">
            <?= htmlspecialchars($primaryCtaText) ?>
          </a>
          <a href="<?= htmlspecialchars($secondaryCtaHref) ?>" class="btn btn-ghost" data-cursor="EXPLORE">
            <?= htmlspecialchars($secondaryCtaText) ?>
          </a>
        </div>
      </div>

      <!-- Right Column: Slide Thumbnails / Hero Media Visual -->
      <div>
        <?php if (!empty($heroSlidesList)): ?>
          <div class="page-hero-slides-track" style="display:flex; flex-direction:column; gap:14px;" data-anim="mask-reveal">
            <?php foreach ($heroSlidesList as $sIdx => $sItem): ?>
              <div style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); padding:16px 20px; display:flex; align-items:center; justify-content:space-between; gap:16px;">
                <div style="display:flex; align-items:center; gap:14px;">
                  <span style="font-family:var(--font-display); font-size:22px; color:var(--red);">0<?= $sIdx + 1 ?></span>
                  <div>
                    <div style="font-size:13px; font-weight:700; color:var(--white); text-transform:uppercase; letter-spacing:0.08em;"><?= htmlspecialchars($sItem['title']) ?></div>
                    <div style="font-size:12px; color:var(--text-3);"><?= htmlspecialchars($sItem['caption']) ?></div>
                  </div>
                </div>
                <span style="color:var(--red); font-size:14px;">→</span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="img-placeholder" style="min-height: 380px;" data-anim="mask-reveal">
            PHOTO — <?= htmlspecialchars($heroTitle) ?> in studio bay
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
