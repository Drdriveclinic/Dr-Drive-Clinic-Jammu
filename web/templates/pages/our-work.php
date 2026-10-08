<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Our Work & Transformations Gallery (`/our-work`)
$galleryItems = $galleryItems ?? [];
?>

<!-- 01. OUR WORK HERO -->
<section class="section section-dark" style="padding-top: 180px; padding-bottom: 90px;">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">STUDIO PORTFOLIO</div>
    <h1 class="h1-hero" style="color:var(--white); margin-bottom: 24px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">REAL VEHICLES.</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">REAL TRANSFORMATIONS.</span></span>
    </h1>
    <div class="redline" style="max-width: 140px; margin-bottom: 24px;" data-anim="redline"></div>
    <p class="lead-text" style="color:var(--grey); max-width: 680px;" data-anim="fade-up">
      A curated showcase of ceramic coatings, self-healing paint protection films, superbike restorations, and deep steam cleans completed at The Drive Clinic in Jammu.
    </p>
  </div>
</section>

<!-- 02. TRANSFORMATIONS GALLERY (Light) -->
<section class="section section-light">
  <div class="container">
    <div class="grid-3-col">
      <?php if (!empty($galleryItems)): ?>
        <?php foreach ($galleryItems as $idx => $item): ?>
          <div class="gallery-item-card" style="background:var(--white); border:1px solid var(--line); overflow:hidden; display:flex; flex-direction:column; justify-content:space-between;" data-anim="mask-reveal">
            <div class="img-placeholder" style="min-height:280px;">
              PHOTO — <?= htmlspecialchars($item['alt']) ?>
            </div>
            <div style="padding:24px;">
              <div style="font-size:11px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:6px;">
                0<?= $idx + 1 ?> / TRANSFORMATION
              </div>
              <h3 class="h3-title" style="font-size:24px; text-transform:uppercase; margin-bottom:8px;">
                <?= htmlspecialchars($item['vehicle_label'] ?: 'Studio Project') ?>
              </h3>
              <p style="color:var(--text-2); font-size:13px; line-height:1.5;">
                <?= htmlspecialchars($item['alt']) ?>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div style="grid-column: 1 / -1; padding:80px 20px; text-align:center; background:var(--white); border:1px solid var(--line);">
          <div class="logo-fallback dark" style="margin-bottom:16px;">[ GALLERY EMPTY STATE ]</div>
          <p style="color:var(--text-2); font-size:15px;">Real customer vehicle transformations and project photos will appear here.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- 03. CTA BAND -->
<?php 
$ctaBandTitle = "READY FOR YOUR TRANSFORMATION?";
$ctaBandLead = "Schedule your detailing, PPF, or wash appointment with our technicians in Nanak Nagar, Jammu.";
require dirname(__DIR__) . '/partials/cta-band.php'; 
?>
