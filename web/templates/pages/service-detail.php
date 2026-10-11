<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Universal Service Detail Template (All 15 Services Data-Driven)
$service = $service ?? [];
$serviceFaqs = $serviceFaqs ?? [];
$serviceGallery = $serviceGallery ?? [];
$nextService = $nextService ?? null;

// JSON Data Decoders
$includedList = !empty($service['included_json']) ? (json_decode($service['included_json'], true) ?: []) : [];
$benefitsList = !empty($service['benefits_json']) ? (json_decode($service['benefits_json'], true) ?: []) : [];
$whoList = !empty($service['who_json']) ? (json_decode($service['who_json'], true) ?: []) : [];
$processList = !empty($service['process_json']) ? (json_decode($service['process_json'], true) ?: []) : [];
$heroSlidesList = !empty($service['hero_slides_json']) ? (json_decode($service['hero_slides_json'], true) ?: []) : [];
$relatedSlugs = !empty($service['related_slugs_json']) ? (json_decode($service['related_slugs_json'], true) ?: []) : [];

// Fetch Related Services
$relatedServicesList = [];
if (!empty($relatedSlugs)) {
    $placeholders = implode(',', array_fill(0, count($relatedSlugs), '?'));
    $relatedServicesList = Database::query("SELECT slug, name, category, short_desc FROM services WHERE slug IN ($placeholders) AND is_active = 1", $relatedSlugs);
}

// Breadcrumb configuration
$heroBreadcrumb = [
    ['label' => 'SERVICES', 'url' => '/services'],
    ['label' => $service['name'], 'url' => '']
];
// Service Visual Images Mapping
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
$svcHeroImg = $serviceImages[$service['slug']] ?? '/assets/img/studio/studio-showcase.png';
$heroVisualImg = $svcHeroImg;
?>

<!-- ========================================================================
     01. SERVICE HERO (PageHero)
     ======================================================================== -->
<?php
$heroEyebrow = strtoupper($service['category']) . ' • ' . strtoupper(str_replace(',', ' & ', $service['vehicle_types']));
$heroTitle = $service['name'];
$heroLead = $service['intro_lead'] ?: $service['short_desc'];
$primaryCtaText = 'BOOK ' . $service['name'] . ' →';
$primaryCtaHref = '/book?service=' . urlencode($service['slug']);
$secondaryCtaText = 'EXPLORE SERVICES →';
$secondaryCtaHref = '/services';
require dirname(__DIR__) . '/partials/page-hero.php';
?>

<!-- ========================================================================
     02. INTRODUCTION: "WHAT IS {SERVICE}?"
     ======================================================================== -->
<?php if (!empty($service['intro_body']) || !empty($service['intro_lead'])): ?>
<section class="section section-light">
  <div class="container grid-5-7">
    <div>
      <div class="eyebrow" data-anim="fade-up">OVERVIEW & PHILOSOPHY</div>
      <h2 class="h2-section" data-anim="text-reveal">
        <span class="anim-text-line"><span class="anim-text-inner">WHAT IS</span></span>
        <span class="anim-text-line"><span class="anim-text-inner text-red"><?= htmlspecialchars($service['name']) ?>?</span></span>
      </h2>
      <p class="lead-text" data-anim="fade-up">
        <?= htmlspecialchars($service['intro_lead'] ?: $service['short_desc']) ?>
      </p>
      <p style="color:var(--text-2); line-height:1.8; margin-bottom:32px;" data-anim="fade-up">
        <?= nl2br(htmlspecialchars($service['intro_body'] ?: $service['short_desc'])) ?>
      </p>
      <div style="display:flex; gap:24px; align-items:center;" data-anim="fade-up">
        <span style="font-size:13px; font-weight:700; text-transform:uppercase; color:var(--text-3);">
          ESTIMATED DURATION: <strong style="color:var(--ink);"><?= htmlspecialchars($service['duration_note'] ?: '[CONFIRM DURATION]') ?></strong>
        </span>
      </div>
    </div>
    <div>
      <div class="service-detail-hero-box" style="overflow:hidden; height:100%; min-height:460px; border-radius:8px; border:1px solid rgba(255,255,255,0.12); box-shadow:0 18px 40px rgba(0,0,0,0.4);" data-anim="mask-reveal">
        <img src="<?= $svcHeroImg ?>" alt="<?= htmlspecialchars($service['name']) ?> in studio bay" style="width:100%; height:100%; object-fit:cover; display:block;">
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ========================================================================
     03. WHY THIS SERVICE / BENEFITS (4-Up Cards, Black top border -> Red hover)
     ======================================================================== -->
<?php if (!empty($benefitsList)): ?>
<section class="section section-white">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">ADVANTAGES & BENEFITS</div>
    <h2 class="h2-section" style="margin-bottom: 48px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">KEY ADVANTAGES OF</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red"><?= htmlspecialchars($service['name']) ?>.</span></span>
    </h2>

    <div class="grid-4-col">
      <?php foreach ($benefitsList as $bIdx => $benefit): ?>
        <div class="benefit-card" style="background:var(--off); border:1px solid var(--line); border-top:2px solid var(--black); padding:32px 24px; display:flex; flex-direction:column; justify-content:space-between; transition: border-top-color 0.2s ease, transform 0.2s ease;" onmouseover="this.style.borderTopColor='var(--red)'; this.style.transform='translateY(-4px)';" onmouseout="this.style.borderTopColor='var(--black)'; this.style.transform='translateY(0)';">
          <div>
            <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:12px;">
              0<?= $bIdx + 1 ?> / BENEFIT
            </div>
            <h4 style="font-family:var(--font-display); font-size:24px; margin-bottom:12px;">
              <?= htmlspecialchars($benefit['title']) ?>
            </h4>
            <p style="color:var(--text-2); font-size:14px; line-height:1.6;">
              <?= htmlspecialchars($benefit['desc']) ?>
            </p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ========================================================================
     04. WHAT'S INCLUDED (Big list with red square bullets)
     ======================================================================== -->
<?php if (!empty($includedList)): ?>
<section class="section section-grey">
  <div class="container grid-50-50">
    <div>
      <div class="eyebrow" data-anim="fade-up">STANDARD PROTOCOL</div>
      <h2 class="h2-section" data-anim="text-reveal">
        <span class="anim-text-line"><span class="anim-text-inner">WHAT'S</span></span>
        <span class="anim-text-line"><span class="anim-text-inner text-red">INCLUDED.</span></span>
      </h2>
      <p class="lead-text" data-anim="fade-up">
        Every step executed with certified detailing procedures, filtered spot-free water, and lint-free equipment.
      </p>
      <div style="margin-top:32px;" data-anim="fade-up">
        <a href="/book?service=<?= htmlspecialchars($service['slug']) ?>" class="btn btn-red">BOOK THIS PACKAGE →</a>
      </div>
    </div>

    <div>
      <div style="background:var(--glass-bg); backdrop-filter:var(--glass-blur); -webkit-backdrop-filter:var(--glass-blur); border:1px solid var(--glass-border); box-shadow:var(--glass-shadow); padding:40px;" data-anim="fade-up">
        <ul style="display:flex; flex-direction:column; gap:20px;">
          <?php foreach ($includedList as $item): ?>
            <li style="display:flex; align-items:flex-start; gap:16px; font-size:16px; font-weight:600; color:var(--ink); line-height:1.5;">
              <span class="tick-square" style="margin-top:6px;"></span>
              <span><?= htmlspecialchars($item) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ========================================================================
     05. SPECIAL MODULE (by service.module)
     ======================================================================== -->
<?php if ($service['module'] === 'ppf'): ?>
  <!-- PPF Special Module: Protected vs Unprotected Slider -->
  <section class="section section-dark">
    <div class="container">
      <div class="eyebrow" data-anim="fade-up">COMPARATIVE ANALYSIS</div>
      <h2 class="h2-section" style="margin-bottom: 32px;" data-anim="text-reveal">
        <span class="anim-text-line"><span class="anim-text-inner">PROTECTED VS</span></span>
        <span class="anim-text-line"><span class="anim-text-inner text-red">UNPROTECTED PAINT.</span></span>
      </h2>
      
      <div class="before-after-container" data-anim="mask-reveal" style="aspect-ratio: 21 / 9;">
        <img src="/assets/img/before-after/paint-before.jpg" alt="Unprotected paint with stone chips and swirls" class="before-after-media">
        <div class="before-after-layer-after">
          <img src="/assets/img/before-after/paint-after.jpg" alt="Protected self-healing PPF barrier" class="before-after-media">
        </div>
        <div class="before-after-divider">
          <div class="before-after-knob">DRAG</div>
        </div>
        <input type="range" min="0" max="100" value="50" class="before-after-input" aria-label="Slide to compare protected versus unprotected paint">
      </div>
    </div>
  </section>

<?php elseif ($service['module'] === 'bike'): ?>
  <!-- Bike Special Module: Horizontal Gallery -->
  <section class="section section-dark" style="overflow:hidden;">
    <div class="container">
      <div class="eyebrow" data-anim="fade-up">PRECISION BIKE ZONES</div>
      <h2 class="h2-section" style="margin-bottom: 40px;" data-anim="text-reveal">
        <span class="anim-text-line"><span class="anim-text-inner">TARGETED</span></span>
        <span class="anim-text-line"><span class="anim-text-inner text-red">MOTORCYCLE ZONES.</span></span>
      </h2>

      <div class="grid-3-col" data-anim="fade-up">
        <div style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); padding:28px;">
          <h4 style="font-family:var(--font-display); font-size:24px; color:var(--red); margin-bottom:8px;">01 / PAINT & TANK</h4>
          <p style="color:var(--grey); font-size:14px; line-height:1.6;">Rider jacket scratch removal and swirl-free tank gloss polish.</p>
        </div>
        <div style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); padding:28px;">
          <h4 style="font-family:var(--font-display); font-size:24px; color:var(--red); margin-bottom:8px;">02 / ENGINE & FINS</h4>
          <p style="color:var(--grey); font-size:14px; line-height:1.6;">Solvent degreasing of cooling fins, crankcase, and tight cylinder blocks.</p>
        </div>
        <div style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); padding:28px;">
          <h4 style="font-family:var(--font-display); font-size:24px; color:var(--red); margin-bottom:8px;">03 / EXHAUST & PIPES</h4>
          <p style="color:var(--grey); font-size:14px; line-height:1.6;">Blueing stain removal, heat oxidation cleanup, and bright chrome polish.</p>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>

<!-- ========================================================================
     06. PROCESS: NUMBERED STEPS
     ======================================================================== -->
<?php if (!empty($processList)): ?>
<section class="section section-white">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">PRECISION PROCEDURE</div>
    <h2 class="h2-section" style="margin-bottom: 48px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">OUR APPLICATION</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">PROCESS.</span></span>
    </h2>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:24px;">
      <?php foreach ($processList as $pStep): ?>
        <div style="background:var(--off); border:1px solid var(--line); border-top:2px solid var(--red); padding:32px 24px; display:flex; flex-direction:column; justify-content:space-between;" data-anim="fade-up">
          <div>
            <div style="font-family:var(--font-display); font-size:36px; color:var(--red); line-height:1; margin-bottom:12px;">
              <?= htmlspecialchars($pStep['step']) ?>
            </div>
            <h4 style="font-family:var(--font-display); font-size:24px; margin-bottom:12px;">
              <?= htmlspecialchars($pStep['title']) ?>
            </h4>
            <p style="color:var(--text-2); font-size:14px; line-height:1.6;">
              <?= htmlspecialchars($pStep['desc']) ?>
            </p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ========================================================================
     07. WHO SHOULD CHOOSE THIS
     ======================================================================== -->
<?php if (!empty($whoList)): ?>
<section class="section section-light">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">SUITABILITY</div>
    <h2 class="h2-section" style="margin-bottom: 48px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">WHO SHOULD CHOOSE</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">THIS SERVICE?</span></span>
    </h2>

    <div class="grid-3-col">
      <?php foreach ($whoList as $who): ?>
        <div style="background:var(--glass-bg); backdrop-filter:var(--glass-blur); -webkit-backdrop-filter:var(--glass-blur); border:1px solid var(--glass-border); box-shadow:var(--glass-shadow); padding:36px 28px; display:flex; flex-direction:column; justify-content:space-between;" data-anim="fade-up">
          <div>
            <div style="color:var(--red); font-size:24px; margin-bottom:12px;">→</div>
            <h4 style="font-family:var(--font-display); font-size:26px; margin-bottom:14px;">
              <?= htmlspecialchars($who['title']) ?>
            </h4>
            <p style="color:var(--text-2); font-size:15px; line-height:1.6;">
              <?= htmlspecialchars($who['desc']) ?>
            </p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ========================================================================
     08. BEFORE / AFTER (Skipped if none)
     ======================================================================== -->
<?php if (!empty($serviceGallery)): ?>
<section class="section section-dark">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">TRANSFORMATION GALLERY</div>
    <h2 class="h2-section" style="margin-bottom: 36px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">REAL VEHICLE</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">RESULTS.</span></span>
    </h2>

    <div class="grid-3-col">
      <?php foreach ($serviceGallery as $gItem): ?>
        <?php 
        $gImg = $gItem['after_image'] ?? $gItem['image'] ?? null;
        if (!$gImg) {
            $gImg = $svcHeroImg;
        }
        ?>
        <div style="background:#181818; border:1px solid rgba(255,255,255,0.1); border-radius:6px; overflow:hidden;" data-anim="mask-reveal">
          <div style="height:250px; overflow:hidden; position:relative;">
            <img src="<?= htmlspecialchars($gImg) ?>" alt="<?= htmlspecialchars($gItem['alt']) ?>" style="width:100%; height:100%; object-fit:cover; display:block; transition:transform 0.4s ease;" loading="lazy" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
          </div>
          <div style="padding:20px;">
            <div style="font-size:11px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:6px;">
              STUDIO TRANSFORMATION
            </div>
            <h4 style="font-size:18px; font-family:var(--font-display); color:var(--white);">
              <?= htmlspecialchars($gItem['vehicle_label'] ?: $service['name']) ?>
            </h4>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ========================================================================
     09. RELATED SERVICES ("YOU MAY ALSO NEED")
     ======================================================================== -->
<?php if (!empty($relatedServicesList)): ?>
<section class="section section-white">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">COMPLEMENTARY PACKAGES</div>
    <h2 class="h2-section" style="margin-bottom: 48px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">YOU MAY ALSO</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">NEED.</span></span>
    </h2>

    <?php require dirname(__DIR__) . '/partials/related-services.php'; ?>
  </div>
</section>
<?php endif; ?>

<!-- ========================================================================
     10. FAQ ACCORDION (With Schema JSON-LD)
     ======================================================================== -->
<?php if (!empty($serviceFaqs)): ?>
<section class="section section-light">
  <div class="container grid-5-7">
    <div>
      <div class="eyebrow" data-anim="fade-up">FREQUENT QUESTIONS</div>
      <h2 class="h2-section" data-anim="text-reveal">
        <span class="anim-text-line"><span class="anim-text-inner">QUESTIONS ABOUT</span></span>
        <span class="anim-text-line"><span class="anim-text-inner text-red"><?= htmlspecialchars($service['name']) ?>.</span></span>
      </h2>
      <p class="lead-text" data-anim="fade-up">
        Have more questions? Reach out to our studio technicians directly on WhatsApp or phone.
      </p>
    </div>

    <div>
      <?php 
      $faqList = $serviceFaqs;
      require dirname(__DIR__) . '/partials/faq-accordion.php'; 
      ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ========================================================================
     11. BOOKING CTA (Dark)
     ======================================================================== -->
<?php 
$ctaBandTitle = "BOOK " . $service['name'] . ".";
$ctaBandLead = "Schedule your studio appointment today. Our team in Nanak Nagar, Jammu will confirm your slot via WhatsApp.";
$ctaBandPrimaryText = "BOOK " . $service['name'] . " →";
$ctaBandPrimaryHref = "/book?service=" . urlencode($service['slug']);
require dirname(__DIR__) . '/partials/cta-band.php';
?>

<!-- ========================================================================
     12. NEXT SERVICE (Full-width Bebas that turns red on hover)
     ======================================================================== -->
<?php if ($nextService): ?>
<section class="section section-white" style="border-top:1px solid var(--line); padding-top:60px; padding-bottom:60px;">
  <div class="container" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:20px;">
    <div>
      <div style="font-size:12px; font-weight:700; color:var(--text-3); letter-spacing:0.2em; text-transform:uppercase; margin-bottom:8px;">
        NEXT SERVICE IN MENU
      </div>
      <h3 class="h3-title" style="font-size:clamp(36px, 5vw, 68px);">
        <a href="/services/<?= htmlspecialchars($nextService['slug']) ?>" style="color:var(--ink); transition:color 0.2s ease;" onmouseover="this.style.color='var(--red)'" onmouseout="this.style.color='var(--ink)'">
          <?= htmlspecialchars($nextService['name']) ?> →
        </a>
      </h3>
    </div>
    <div>
      <a href="/services/<?= htmlspecialchars($nextService['slug']) ?>" class="btn btn-ghost-dark">VIEW NEXT →</a>
    </div>
  </div>
</section>
<?php endif; ?>
