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
          <?php 
          $cardImg = $item['after_image'] ?? $item['image'] ?? null;
          if (!$cardImg) {
              $fallbackImages = [
                  '/assets/img/services/fortuner-ppf-india.jpg',
                  '/assets/img/services/ceramic-coating-india.jpg',
                  '/assets/img/bike/superbike-detailing-india.jpg',
                  '/assets/img/services/full-detailing.jpg',
                  '/assets/img/wash/ceramic-wash.jpg',
                  '/assets/img/before-after/interior-after.jpg'
              ];
              $cardImg = $fallbackImages[$idx % count($fallbackImages)];
          }
          ?>
          <div class="gallery-item-card" style="background:var(--glass-bg); backdrop-filter:var(--glass-blur); -webkit-backdrop-filter:var(--glass-blur); border:1px solid var(--glass-border); box-shadow:var(--glass-shadow); border-radius:8px; overflow:hidden; display:flex; flex-direction:column; justify-content:space-between;" data-anim="mask-reveal">
            <div class="gallery-card-thumb" style="height:270px; overflow:hidden; position:relative;">
              <img src="<?= htmlspecialchars($cardImg) ?>" alt="<?= htmlspecialchars($item['alt']) ?>" style="width:100%; height:100%; object-fit:cover; display:block; transition:transform 0.4s ease;" loading="lazy" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
              <span style="position:absolute; top:14px; right:14px; background:rgba(8,11,18,0.85); backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,0.18); color:var(--white); font-size:10px; font-weight:800; padding:4px 9px; border-radius:4px; letter-spacing:0.12em; text-transform:uppercase;">
                0<?= $idx + 1 ?> / TRANSFORM
              </span>
            </div>
            <div style="padding:24px;">
              <div style="font-size:11px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:6px;">
                STUDIO CRAFTSMANSHIP
              </div>
              <h3 class="h3-title" style="font-size:22px; text-transform:uppercase; margin-bottom:8px; color:var(--white);">
                <?= htmlspecialchars($item['vehicle_label'] ?: 'Studio Transformation') ?>
              </h3>
              <p style="color:var(--text-2); font-size:13px; line-height:1.5;">
                <?= htmlspecialchars($item['alt']) ?>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div style="grid-column: 1 / -1; padding:80px 20px; text-align:center; background:var(--glass-bg); backdrop-filter:var(--glass-blur); -webkit-backdrop-filter:var(--glass-blur); border:1px solid var(--glass-border); box-shadow:var(--glass-shadow);">
          <div class="logo-fallback" style="margin-bottom:16px;">[ GALLERY EMPTY STATE ]</div>
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
