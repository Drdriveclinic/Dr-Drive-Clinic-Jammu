<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Services Directory Template (`/services`)
$services = $services ?? [];

// Helper to partition cards
$ppf = null;
$bike = null;
$otherServices = [];

$serviceImages = [
    'ppf'                   => '/assets/img/studio/ppf-install.png',
    'ceramic-coating'       => '/assets/img/studio/ceramic-coating.png',
    'bike-detailing'        => '/assets/img/studio/bike-detailing.png',
    'full-car-detailing'    => '/assets/img/studio/full-detailing.png',
    'interior-deep-clean'   => '/assets/img/studio/interior-clean.png',
    'ceramic-wash'          => '/assets/img/studio/ceramic-wash.png',
    'steam-wash'            => '/assets/img/studio/steam-wash.png',
    'basic-car-wash'        => '/assets/img/studio/basic-wash.png',
    'engine-detailing'      => '/assets/img/studio/engine-bay.png',
    'polishing-waxing'      => '/assets/img/studio/polishing-waxing.png',
    'tyre-rim-care'         => '/assets/img/studio/wheel-care.png',
    'headlight-restoration' => '/assets/img/services/headlight.jpg',
    'underbody-coating'     => '/assets/img/services/underbody.jpg',
    'glass-treatment'       => '/assets/img/services/glass-treatment.jpg',
    'rubbing-compound'      => '/assets/img/studio/polishing-waxing.png',
];

foreach ($services as $svc) {
    if ($svc['slug'] === 'ppf') $ppf = $svc;
    elseif ($svc['slug'] === 'bike-detailing') $bike = $svc;
    else $otherServices[] = $svc;
}
?>

<!-- 01. DIRECTORY HERO -->
<section class="section section-dark" style="padding-top: 180px; padding-bottom: 90px;">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">SERVICES DIRECTORY</div>
    <h1 class="h1-hero" style="color:var(--white); margin-bottom: 24px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">EVERY SERVICE.</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">ONE STANDARD.</span></span>
    </h1>
    <div class="redline" style="max-width: 140px; margin-bottom: 24px;" data-anim="redline"></div>
    <p class="lead-text" style="color:var(--grey); max-width: 640px;" data-anim="fade-up">
      Explore our comprehensive suite of certified automotive detailing, self-healing paint protection film, ceramic coatings, and studio wash programs in Jammu.
    </p>
  </div>
</section>

<!-- 02. DIRECTORY FILTER TABS & EDITORIAL GRID -->
<section class="section section-light" id="services-grid-section">
  <div class="container">
    <!-- Filter Tabs -->
    <div class="service-explorer-tabs" role="tablist" style="margin-bottom: 60px;">
      <button class="explorer-tab-btn active services-filter-btn" data-filter="all" role="tab" aria-selected="true">ALL SERVICES</button>
      <button class="explorer-tab-btn services-filter-btn" data-filter="protect" role="tab" aria-selected="false">PROTECT</button>
      <button class="explorer-tab-btn services-filter-btn" data-filter="detail" role="tab" aria-selected="false">DETAIL</button>
      <button class="explorer-tab-btn services-filter-btn" data-filter="clean" role="tab" aria-selected="false">CLEAN</button>
    </div>

    <!-- Editorial Mixed Grid -->
    <div class="services-editorial-grid" style="display:flex; flex-direction:column; gap:36px;">
      
      <!-- Top Row: 2 Full-Width Hero Cards (PPF & Bike Detailing) -->
      <div class="grid-50-50 services-hero-pair">
        <?php if ($ppf): ?>
          <div class="service-card-item" data-category="<?= htmlspecialchars($ppf['category']) ?>" style="background:var(--glass-bg); backdrop-filter:var(--glass-blur); -webkit-backdrop-filter:var(--glass-blur); border:1px solid var(--glass-border); box-shadow:var(--glass-shadow); border-radius:6px; overflow:hidden; display:flex; flex-direction:column; justify-content:space-between;">
            <div style="overflow:hidden; height:320px; border-bottom:1px solid var(--glass-border);" data-anim="mask-reveal">
              <img src="<?= $serviceImages['ppf'] ?>" alt="<?= htmlspecialchars($ppf['name']) ?>" style="width:100%; height:100%; object-fit:cover; display:block;">
            </div>
            <div style="padding:36px;">
              <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">
                01 / <?= htmlspecialchars(strtoupper($ppf['category'])) ?>
              </div>
              <h3 class="h3-title" style="font-size:36px; margin-bottom:14px;">
                <a href="/services/<?= htmlspecialchars($ppf['slug']) ?>" style="color:inherit; text-decoration:none;"><?= htmlspecialchars($ppf['name']) ?></a>
              </h3>
              <p style="color:var(--text-2); font-size:15px; line-height:1.6; margin-bottom:28px;">
                <?= htmlspecialchars($ppf['short_desc']) ?>
              </p>
              <a href="/services/<?= htmlspecialchars($ppf['slug']) ?>" class="btn btn-red">VIEW SERVICE →</a>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($bike): ?>
          <div class="service-card-item" data-category="<?= htmlspecialchars($bike['category']) ?>" style="background:var(--glass-bg); backdrop-filter:var(--glass-blur); -webkit-backdrop-filter:var(--glass-blur); border:1px solid var(--glass-border); box-shadow:var(--glass-shadow); border-radius:6px; overflow:hidden; display:flex; flex-direction:column; justify-content:space-between;">
            <div style="overflow:hidden; height:320px; border-bottom:1px solid var(--glass-border);" data-anim="mask-reveal">
              <img src="<?= $serviceImages['bike-detailing'] ?>" alt="<?= htmlspecialchars($bike['name']) ?>" style="width:100%; height:100%; object-fit:cover; display:block;">
            </div>
            <div style="padding:36px;">
              <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">
                02 / <?= htmlspecialchars(strtoupper($bike['category'])) ?>
              </div>
              <h3 class="h3-title" style="font-size:36px; margin-bottom:14px;">
                <a href="/services/<?= htmlspecialchars($bike['slug']) ?>" style="color:inherit; text-decoration:none;"><?= htmlspecialchars($bike['name']) ?></a>
              </h3>
              <p style="color:var(--text-2); font-size:15px; line-height:1.6; margin-bottom:28px;">
                <?= htmlspecialchars($bike['short_desc']) ?>
              </p>
              <a href="/services/<?= htmlspecialchars($bike['slug']) ?>" class="btn btn-red">VIEW SERVICE →</a>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Mixed Grid of Other Services (Large, Small, Wide) -->
      <div class="grid-3-col services-mixed-flow">
        <?php foreach ($otherServices as $idx => $svc): 
          $itemNumber = str_pad((string)($idx + 3), 2, '0', STR_PAD_LEFT);
          $imgSrc = $serviceImages[$svc['slug']] ?? '/assets/img/services/full-detailing.jpg';
        ?>
          <div class="service-card-item" data-category="<?= htmlspecialchars($svc['category']) ?>" style="background:var(--glass-bg); backdrop-filter:var(--glass-blur); -webkit-backdrop-filter:var(--glass-blur); border:1px solid var(--glass-border); box-shadow:var(--glass-shadow); border-radius:6px; overflow:hidden; display:flex; flex-direction:column; justify-content:space-between; transition: transform var(--dur-fast) ease, border-color var(--dur-fast) ease, background var(--dur-fast) ease;">
            <div style="overflow:hidden; height:220px; border-bottom:1px solid var(--glass-border);" data-anim="mask-reveal">
              <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($svc['name']) ?>" style="width:100%; height:100%; object-fit:cover; display:block;">
            </div>
            <div style="padding:28px; display:flex; flex-direction:column; justify-content:space-between; flex:1;">
              <div>
                <div style="font-size:11px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">
                  <?= $itemNumber ?> / <?= htmlspecialchars(strtoupper($svc['category'])) ?>
                </div>
                <h4 style="font-family:var(--font-display); font-size:26px; margin-bottom:12px;">
                  <a href="/services/<?= htmlspecialchars($svc['slug']) ?>"><?= htmlspecialchars($svc['name']) ?></a>
                </h4>
                <p style="color:var(--text-2); font-size:14px; line-height:1.6; margin-bottom:24px;">
                  <?= htmlspecialchars($svc['short_desc']) ?>
                </p>
              </div>
              <a href="/services/<?= htmlspecialchars($svc['slug']) ?>" class="text-link">VIEW SERVICE →</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- 03. DIAGNOSIS TEASER & CTA BAND -->
<section class="section section-white" style="text-align:center;">
  <div class="container">
    <div class="eyebrow" style="justify-content:center;" data-anim="fade-up">NOT SURE WHAT TO CHOOSE?</div>
    <h2 class="h2-section" style="margin-bottom: 24px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">TRY OUR VEHICLE</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">DIAGNOSIS.</span></span>
    </h2>
    <p class="lead-text" style="max-width: 600px; margin: 0 auto 36px;" data-anim="fade-up">
      Answer one simple question about what you're looking to improve, and receive an instant studio service recommendation.
    </p>
    <a href="/#diagnosis" class="btn btn-red" data-anim="fade-up">TRY DIAGNOSIS WIDGET →</a>
  </div>
</section>

<?php require dirname(__DIR__) . '/partials/cta-band.php'; ?>

<!-- Client-side filter script for tabs (preserving SEO markup) -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const filterBtns = document.querySelectorAll('.services-filter-btn');
  const serviceCards = document.querySelectorAll('.service-card-item');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const filter = btn.dataset.filter;
      
      filterBtns.forEach(b => {
        b.classList.remove('active');
        b.setAttribute('aria-selected', 'false');
      });
      btn.classList.add('active');
      btn.setAttribute('aria-selected', 'true');

      serviceCards.forEach(card => {
        if (filter === 'all' || card.dataset.category === filter) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
});
</script>
