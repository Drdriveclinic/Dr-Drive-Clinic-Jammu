<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Home Page Template (All 21 Sections Faithful Build)
$heroSlides = $heroSlides ?? [];
$services = $services ?? [];
$galleryItems = $galleryItems ?? [];
$faqs = $faqs ?? [];

// Organize services by category for Section 03 Service Explorer
$protectServices = array_filter($services, fn($s) => $s['category'] === 'protect');
$detailServices = array_filter($services, fn($s) => $s['category'] === 'detail');
$cleanServices = array_filter($services, fn($s) => $s['category'] === 'clean');

// Hero Dynamic Service Slides
$textSlides = [
  [
    'tag' => 'PPF Armor',
    'badge' => '01 / PAINT PROTECTION FILM',
    'lead_keyword' => 'SELF-HEALING PPF ARMOR.',
    'supporting' => "Instant self-healing TPU armor defending original factory paint against highway gravel, micro-scratches, and Jammu's scorching UV sun.",
    'service_slug' => 'ppf',
    'cta_label' => 'EXPLORE PPF SPECS →'
  ],
  [
    'tag' => '9H Ceramic',
    'badge' => '02 / 9H NANO CERAMIC COATING',
    'lead_keyword' => '9H CERAMIC GLASS MATRIX.',
    'supporting' => 'Nanotechnology liquid quartz bonding permanently to clear coat for intense candy reflections, chemical resistance, and water repellency.',
    'service_slug' => 'ceramic-coating',
    'cta_label' => 'EXPLORE 9H CERAMIC →'
  ],
  [
    'tag' => 'Full Detailing',
    'badge' => '03 / FULL CAR RESTORATION',
    'lead_keyword' => 'COMPLETE CAR RESET.',
    'supporting' => 'Two-stage machine paint correction, dry steam interior sterilization, leather feeding, and engine bay detailing for total vehicle rejuvenation.',
    'service_slug' => 'full-car-detailing',
    'cta_label' => 'VIEW DETAILING SPECS →'
  ],
  [
    'tag' => 'Studio Wash',
    'badge' => '04 / CERAMIC & STEAM WASH',
    'lead_keyword' => 'SWIRL-FREE STUDIO WASH.',
    'supporting' => 'Dual-bucket grit-guarded washes with SiO2 ceramic gloss boosters, spot-free filtered water, and hospital-grade interior steam sanitization.',
    'service_slug' => 'ceramic-wash',
    'cta_label' => 'VIEW WASH PROGRAMS →'
  ],
  [
    'tag' => 'Superbikes',
    'badge' => '05 / BIKE & SUPERBIKE CARE',
    'lead_keyword' => 'PRECISION TWO-WHEEL CARE.',
    'supporting' => 'Bespoke precision detailing tailored for superbikes and performance machines — engine fin degreasing, fairing PPF, and ceramic shields.',
    'service_slug' => 'bike-detailing',
    'cta_label' => 'EXPLORE BIKE CARE →'
  ]
];
?>

<!-- ========================================================================
     01. CINEMATIC VIDEO HERO (Clean, Dynamic & Minimalist Architecture)
     ======================================================================== -->
<section class="hero-cinema" aria-label="The Drive Clinic Automotive Studio">
  <!-- Persistent Ambient Video Background -->
  <div class="hero-video-wrap">
    <video class="hero-bg-video" autoplay loop muted playsinline preload="auto" poster="/assets/img/hero/hero-1-ppf.jpg">
      <source src="/assets/video/hero-cinematic.mp4" type="video/mp4">
    </video>
    <div class="hero-video-overlay-gradient"></div>
    <div class="hero-video-vignette"></div>
  </div>

  <div class="container hero-cinema-container">
    <div class="hero-cinema-main">
      <!-- Minimalist Studio Eyebrow -->
      <div class="hero-eyebrow" data-anim="fade-up">
        <span class="eyebrow-pulse-dot"></span>
        <span class="hero-eyebrow-badge" id="heroBadge">01 / PAINT PROTECTION FILM</span>
        <span class="hero-eyebrow-sep">•</span>
        <span class="hero-eyebrow-loc">NANAK NAGAR, JAMMU</span>
      </div>

      <!-- Main Clean Dynamic Headline -->
      <h1 class="hero-cinema-title" data-anim="text-reveal">
        <span class="hero-title-static">ARCHITECTS OF</span>
        <span class="hero-title-dynamic text-red" id="heroDynamicTitle">SELF-HEALING PPF ARMOR.</span>
      </h1>

      <div class="redline hero-clean-redline" data-anim="redline"></div>

      <!-- Single Clear Descriptive Statement -->
      <p class="hero-cinema-lead lead-text" id="heroDynamicDesc" data-anim="fade-up">
        Instant self-healing TPU armor defending original factory paint against highway gravel, micro-scratches, and Jammu's scorching UV sun.
      </p>

      <!-- Crisp Dual Action Buttons -->
      <div class="hero-cinema-actions" data-anim="fade-up">
        <a href="/book?service=ppf" id="heroBookBtn" class="btn btn-red hero-btn-main" data-cursor="BOOK">
          BOOK APPOINTMENT →
        </a>
        <a href="/services/ppf" id="heroDetailBtn" class="btn btn-ghost hero-btn-sub" data-cursor="EXPLORE">
          EXPLORE PPF SPECS →
        </a>
      </div>
    </div>

    <!-- Minimalist Dynamic Slide Nav Dock -->
    <div class="hero-nav-dock" data-anim="fade-up">
      <div class="hero-dock-counter">
        <span class="dock-current" id="heroDockCurrent">01</span>
        <span class="dock-sep">/</span>
        <span class="dock-total">05</span>
      </div>

      <div class="hero-dock-items" role="tablist" aria-label="Featured Studio Services">
        <?php foreach ($textSlides as $idx => $s): ?>
          <button type="button" class="hero-dock-btn <?= $idx === 0 ? 'is-active' : '' ?>" data-index="<?= $idx ?>" role="tab" aria-selected="<?= $idx === 0 ? 'true' : 'false' ?>" aria-label="<?= htmlspecialchars($s['tag']) ?>">
            <span class="dock-btn-label"><?= htmlspecialchars($s['tag']) ?></span>
            <span class="dock-btn-bar"><span class="dock-btn-fill"></span></span>
          </button>
        <?php endforeach; ?>
      </div>

      <div class="hero-dock-arrows">
        <button type="button" class="dock-arrow-btn" id="heroPrevBtn" aria-label="Previous Service">←</button>
        <button type="button" class="dock-arrow-btn" id="heroNextBtn" aria-label="Next Service">→</button>
      </div>
    </div>
  </div>

  <!-- Hidden JSON Data for Smooth Dynamic Switcher -->
  <script id="heroSlidesData" type="application/json">
    <?= json_encode($textSlides, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
  </script>
</section>

<!-- ========================================================================
     02. BRAND INTRO (White Marquee + 5/7 Grid)
     ======================================================================== -->
<div class="marquee-strip" aria-hidden="true">
  <div class="marquee-track">
    <div class="marquee-item">PROTECT <span class="marquee-separator">/</span></div>
    <div class="marquee-item outline">DETAIL <span class="marquee-separator">/</span></div>
    <div class="marquee-item">RESTORE <span class="marquee-separator">/</span></div>
    <div class="marquee-item outline">MAINTAIN <span class="marquee-separator">/</span></div>
    <div class="marquee-item">PROTECT <span class="marquee-separator">/</span></div>
    <div class="marquee-item outline">DETAIL <span class="marquee-separator">/</span></div>
    <div class="marquee-item">RESTORE <span class="marquee-separator">/</span></div>
    <div class="marquee-item outline">MAINTAIN <span class="marquee-separator">/</span></div>
  </div>
</div>

<section class="section section-light">
  <div class="container grid-5-7">
    <div>
      <div class="eyebrow" data-anim="fade-up">WELCOME TO THE STUDIO</div>
      <h2 class="h2-section" data-anim="text-reveal">
        <span class="anim-text-line"><span class="anim-text-inner">WELCOME TO</span></span>
        <span class="anim-text-line"><span class="anim-text-inner text-red">THE DRIVE CLINIC.</span></span>
      </h2>
      <p class="lead-text" data-anim="fade-up">
        Precision detailing, protection and care for the vehicles you care about.
      </p>
      <p style="color:var(--text-2); margin-bottom: 32px; line-height: 1.7;" data-anim="fade-up">
        Located in Nanak Nagar, Jammu, The Drive Clinic was engineered to bring world-class automotive surface care, clean-room PPF bays, and pH-balanced water filtration to passionate car and bike owners across Jammu & Kashmir.
      </p>
      <div style="display:flex; gap:32px;" data-anim="fade-up">
        <a href="/services" class="text-link">EXPLORE ALL SERVICES →</a>
        <a href="/about" class="text-link">ABOUT OUR STUDIO →</a>
      </div>
    </div>
    <div>
      <div style="border-radius:8px; overflow:hidden; min-height:460px; height:100%; border:1px solid rgba(255,255,255,0.12); box-shadow:0 18px 40px rgba(0,0,0,0.4);" data-anim="mask-reveal">
        <img src="/assets/img/studio/studio-showcase.png" alt="The DDC Detailing Studio Bay" style="width:100%; height:100%; object-fit:cover; display:block;">
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================
     03. WHAT DOES YOUR RIDE NEED? (Service Explorer)
     ======================================================================== -->
<?php
$serviceStudioImages = [
  'ppf'                 => '/assets/img/studio/ppf-install.png',
  'ceramic-coating'     => '/assets/img/studio/ceramic-coating.png',
  'full-car-detailing'  => '/assets/img/studio/full-detailing.png',
  'interior-deep-clean' => '/assets/img/studio/interior-clean.png',
  'bike-detailing'      => '/assets/img/studio/bike-detailing.png',
  'ceramic-wash'        => '/assets/img/studio/ceramic-wash.png',
  'steam-wash'          => '/assets/img/studio/steam-wash.png',
  'basic-car-wash'      => '/assets/img/studio/basic-wash.png',
  'engine-detailing'    => '/assets/img/studio/engine-bay.png',
  'polishing-waxing'    => '/assets/img/studio/polishing-waxing.png',
  'tyre-rim-care'       => '/assets/img/studio/wheel-care.png'
];
?>
<section class="section section-white service-explorer">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">SERVICE EXPLORER</div>
    <h2 class="h2-section" style="margin-bottom: 40px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">WHAT DOES YOUR</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">RIDE NEED?</span></span>
    </h2>

    <div class="service-explorer-tabs" role="tablist">
      <button class="explorer-tab-btn active" data-category="protect" role="tab" aria-selected="true">PROTECT</button>
      <button class="explorer-tab-btn" data-category="detail" role="tab" aria-selected="false">DETAIL</button>
      <button class="explorer-tab-btn" data-category="clean" role="tab" aria-selected="false">CLEAN</button>
    </div>

    <div class="grid-5-7" style="align-items:flex-start;">
      <!-- Left Column: Service Lists -->
      <div>
        <!-- Group 1: PROTECT -->
        <div class="explorer-group-list is-active" data-category="protect">
          <?php foreach ($protectServices as $idx => $svc): 
            $svcImg = $serviceStudioImages[$svc['slug']] ?? '/assets/img/studio/ppf-install.png';
          ?>
            <div class="explorer-service-item <?= $idx === 0 ? 'is-active is-expanded' : '' ?>" 
                 data-slug="<?= htmlspecialchars($svc['slug']) ?>"
                 data-image="<?= htmlspecialchars($svcImg) ?>"
                 data-name="<?= htmlspecialchars($svc['name']) ?>" 
                 data-caption="<?= htmlspecialchars($svc['name']) ?> — Studio Bay Treatment">
              <div class="explorer-service-header">
                <span class="explorer-service-title"><?= htmlspecialchars($svc['name']) ?></span>
                <span class="explorer-service-icon" style="font-size:24px; color:var(--red);">+</span>
              </div>
              <div class="explorer-service-body">
                <div class="explorer-service-content">
                  <p><?= htmlspecialchars($svc['short_desc']) ?></p>
                  <div style="margin-top:16px;">
                    <a href="/services/<?= htmlspecialchars($svc['slug']) ?>" class="text-link">VIEW SERVICE SPECS →</a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Group 2: DETAIL -->
        <div class="explorer-group-list" data-category="detail">
          <?php foreach ($detailServices as $idx => $svc): 
            $svcImg = $serviceStudioImages[$svc['slug']] ?? '/assets/img/studio/full-detailing.png';
          ?>
            <div class="explorer-service-item" 
                 data-slug="<?= htmlspecialchars($svc['slug']) ?>"
                 data-image="<?= htmlspecialchars($svcImg) ?>"
                 data-name="<?= htmlspecialchars($svc['name']) ?>" 
                 data-caption="<?= htmlspecialchars($svc['name']) ?> — Detailing Bay Precision">
              <div class="explorer-service-header">
                <span class="explorer-service-title"><?= htmlspecialchars($svc['name']) ?></span>
                <span class="explorer-service-icon" style="font-size:24px; color:var(--red);">+</span>
              </div>
              <div class="explorer-service-body">
                <div class="explorer-service-content">
                  <p><?= htmlspecialchars($svc['short_desc']) ?></p>
                  <div style="margin-top:16px;">
                    <a href="/services/<?= htmlspecialchars($svc['slug']) ?>" class="text-link">VIEW SERVICE SPECS →</a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Group 3: CLEAN -->
        <div class="explorer-group-list" data-category="clean">
          <?php foreach ($cleanServices as $idx => $svc): 
            $svcImg = $serviceStudioImages[$svc['slug']] ?? '/assets/img/studio/ceramic-wash.png';
          ?>
            <div class="explorer-service-item" 
                 data-slug="<?= htmlspecialchars($svc['slug']) ?>"
                 data-image="<?= htmlspecialchars($svcImg) ?>"
                 data-name="<?= htmlspecialchars($svc['name']) ?>" 
                 data-caption="<?= htmlspecialchars($svc['name']) ?> — Studio Wash Program">
              <div class="explorer-service-header">
                <span class="explorer-service-title"><?= htmlspecialchars($svc['name']) ?></span>
                <span class="explorer-service-icon" style="font-size:24px; color:var(--red);">+</span>
              </div>
              <div class="explorer-service-body">
                <div class="explorer-service-content">
                  <p><?= htmlspecialchars($svc['short_desc']) ?></p>
                  <div style="margin-top:16px;">
                    <a href="/services/<?= htmlspecialchars($svc['slug']) ?>" class="text-link">VIEW SERVICE SPECS →</a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Right Column: Dynamic Sticky Image Preview -->
      <div>
        <div class="explorer-sticky-card" style="position:sticky; top:110px; border-radius:8px; overflow:hidden; border:1px solid rgba(255,255,255,0.14); box-shadow:0 20px 48px rgba(0,0,0,0.55); background:#0F131D;">
          <div style="position:relative; width:100%; height:480px; overflow:hidden;">
            <img src="/assets/img/studio/ppf-install.png" alt="The DDC Detailing Service" class="explorer-preview-img" style="width:100%; height:100%; object-fit:cover; display:block; transition: opacity 0.25s ease, transform 0.35s ease;">
            <div style="position:absolute; inset:0; background:linear-gradient(180deg, rgba(0,0,0,0.1) 40%, rgba(9,11,16,0.92) 100%); pointer-events:none;"></div>
            <div style="position:absolute; bottom:0; left:0; right:0; padding:24px 22px; pointer-events:none;">
              <div style="font-size:11px; font-weight:800; color:var(--red); letter-spacing:0.18em; text-transform:uppercase; margin-bottom:4px;">THE DDC STUDIO BAY</div>
              <div class="explorer-preview-title" style="font-family:var(--font-display); font-size:26px; color:#FFFFFF; text-shadow:0 2px 10px rgba(0,0,0,0.85); line-height:1.1;">PAINT PROTECTION FILM</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================
     04. PPF FEATURE (DARK)
     ======================================================================== -->
<section class="section section-dark">
  <div class="container grid-50-50">
    <div>
      <div style="overflow:hidden; height:100%; min-height:440px; border-radius:8px; border:1px solid rgba(255,255,255,0.12); box-shadow:0 18px 40px rgba(0,0,0,0.4);" data-anim="mask-reveal">
        <img src="/assets/img/studio/ppf-install.png" alt="Self-healing Paint Protection Film installation at The DDC" style="width:100%; height:100%; object-fit:cover; display:block;">
      </div>
    </div>
    <div>
      <div class="eyebrow" data-anim="fade-up">PAINT PROTECTION FILM</div>
      <h2 class="h2-section" data-anim="text-reveal">
        <span class="anim-text-line"><span class="anim-text-inner">PROTECT</span></span>
        <span class="anim-text-line"><span class="anim-text-inner">WHAT YOU</span></span>
        <span class="anim-text-line"><span class="anim-text-inner text-red">DRIVE.</span></span>
      </h2>
      <p class="lead-text" style="color:var(--grey);" data-anim="fade-up">
        High-grade aliphatic polyurethane with elastomeric self-healing matrix that repels highway stone chips and surface scratches.
      </p>

      <div class="tick-list" data-anim="fade-up">
        <div class="tick-item"><span class="tick-square"></span> Scratch Defense</div>
        <div class="tick-item"><span class="tick-square"></span> Stone-Chip Shield</div>
        <div class="tick-item"><span class="tick-square"></span> UV Protection</div>
        <div class="tick-item"><span class="tick-square"></span> Paint Preservation</div>
      </div>

      <div style="display:flex; gap:18px; flex-wrap:wrap;" data-anim="fade-up">
        <a href="/services/ppf" class="btn btn-red">EXPLORE PPF →</a>
        <a href="/book?service=ppf" class="btn btn-ghost">GET A PPF QUOTE →</a>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================
     05. INTERACTIVE PPF VEHICLE (DARK)
     ======================================================================== -->
<section class="section section-dark" style="padding-top: 0;">
  <div class="container">
    <div class="eyebrow" style="margin-bottom: 16px;">VULNERABILITY MAP</div>
    <h3 class="h3-title" style="margin-bottom: 32px;">SELECT A ZONE TO EXPLORE IMPACT RISK</h3>

    <div class="ppf-hotspot-widget">
      <!-- Car Side-Profile SVG Canvas -->
      <div class="hotspot-car-stage">
        <svg viewBox="0 0 800 280" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%; height:auto;">
          <!-- Front Tint Overlay (28% Opacity) -->
          <path class="svg-zone-highlight" data-zone="hood" d="M120,130 L260,130 L220,90 L150,90 Z" fill="rgba(229,9,20,0.28)" />
          <path class="svg-zone-highlight" data-zone="bumper" d="M60,180 L140,180 L120,130 L60,140 Z" fill="rgba(229,9,20,0.28)" />
          <path class="svg-zone-highlight" data-zone="fender" d="M110,130 L230,130 L210,180 L130,180 Z" fill="rgba(229,9,20,0.28)" />
          <path class="svg-zone-highlight" data-zone="orvm" d="M260,95 L290,95 L285,115 L260,115 Z" fill="rgba(229,9,20,0.28)" />
          <path class="svg-zone-highlight" data-zone="headlight" d="M70,135 L110,135 L100,155 L65,150 Z" fill="rgba(229,9,20,0.28)" />

          <!-- Car Outline in White Strokes -->
          <path d="M50,180 Q60,120 120,110 L220,90 L340,40 Q440,35 560,70 L720,110 Q760,130 760,180 L730,190 Q720,220 670,220 Q620,220 610,190 L260,190 Q250,220 200,220 Q150,220 140,190 Z" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="square" />
          <!-- Wheels -->
          <circle cx="200" cy="190" r="36" stroke="#FFFFFF" stroke-width="2.5" fill="#080808" />
          <circle cx="200" cy="190" r="18" stroke="#E50914" stroke-width="2" />
          <circle cx="670" cy="190" r="36" stroke="#FFFFFF" stroke-width="2.5" fill="#080808" />
          <circle cx="670" cy="190" r="18" stroke="#E50914" stroke-width="2" />
          <!-- Cabin Windows -->
          <path d="M250,95 L340,55 L520,75 L490,110 L265,110 Z" stroke="#FFFFFF" stroke-width="1.5" fill="none" />
          <path d="M535,78 L650,110 L510,110 Z" stroke="#FFFFFF" stroke-width="1.5" fill="none" />
        </svg>

        <!-- Hotspot Coordinate Buttons (44px hit areas) -->
        <button class="hotspot-btn is-active" data-zone="hood" style="top: 36%; left: 24%;" aria-label="Zone: Hood">1</button>
        <button class="hotspot-btn" data-zone="bumper" style="top: 54%; left: 10%;" aria-label="Zone: Bumper">2</button>
        <button class="hotspot-btn" data-zone="fender" style="top: 52%; left: 21%;" aria-label="Zone: Fender">3</button>
        <button class="hotspot-btn" data-zone="orvm" style="top: 38%; left: 36%;" aria-label="Zone: ORVM">4</button>
        <button class="hotspot-btn" data-zone="headlight" style="top: 48%; left: 11%;" aria-label="Zone: Headlight">5</button>
      </div>

      <!-- 5-Column Synced Zone Panel -->
      <div class="hotspot-zones-grid">
        <div class="hotspot-zone-card is-active" data-zone="hood">
          <div class="hotspot-zone-title">01 / HOOD</div>
          <div class="hotspot-zone-desc">Absorbs 65% of high-speed airborne stone impacts and direct sun UV exposure.</div>
        </div>
        <div class="hotspot-zone-card" data-zone="bumper">
          <div class="hotspot-zone-title">02 / BUMPER</div>
          <div class="hotspot-zone-desc">Primary barrier against road gravel, parking scuffs, and high-velocity debris.</div>
        </div>
        <div class="hotspot-zone-card" data-zone="fender">
          <div class="hotspot-zone-title">03 / FENDER</div>
          <div class="hotspot-zone-desc">Guards against tire sling, kicked-up sand, and wheel-arch stone abrasions.</div>
        </div>
        <div class="hotspot-zone-card" data-zone="orvm">
          <div class="hotspot-zone-title">04 / ORVM</div>
          <div class="hotspot-zone-desc">Protruding aerodynamic mirrors prone to tight city scraping and insect etching.</div>
        </div>
        <div class="hotspot-zone-card" data-zone="headlight">
          <div class="hotspot-zone-title">05 / HEADLIGHT</div>
          <div class="hotspot-zone-desc">Prevents polycarbonate lens yellowing, clouding, and costly rock breakage.</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================
     06. BIKE DETAILING (LIGHT)
     ======================================================================== -->
<section class="section section-light">
  <div class="container grid-50-50">
    <div>
      <div class="eyebrow" data-anim="fade-up">MOTORCYCLE PRECISION CARE</div>
      <h2 class="h2-section" data-anim="text-reveal">
        <span class="anim-text-line"><span class="anim-text-inner">TWO WHEELS.</span></span>
        <span class="anim-text-line"><span class="anim-text-inner text-red">FULL ATTENTION.</span></span>
      </h2>
      <p class="lead-text" data-anim="fade-up">
        Dedicated mechanical degreasing, chrome restoration, and paint gloss enhancement tailored for two-wheelers.
      </p>

      <div class="tick-list" data-anim="fade-up">
        <div class="tick-item"><span class="tick-square"></span> Paint Restoration</div>
        <div class="tick-item"><span class="tick-square"></span> Engine Fin De-greasing</div>
        <div class="tick-item"><span class="tick-square"></span> Alloy Wheels & Spokes</div>
        <div class="tick-item"><span class="tick-square"></span> Tyres & Chain Lube</div>
        <div class="tick-item"><span class="tick-square"></span> Exhaust Header Polish</div>
        <div class="tick-item"><span class="tick-square"></span> Exposed Components</div>
      </div>

      <a href="/services/bike-detailing" class="btn btn-red" data-anim="fade-up">DETAIL MY BIKE →</a>
    </div>

    <!-- 3-Image Mosaic -->
    <div class="bike-mosaic-grid" data-anim="mask-reveal">
      <div style="overflow:hidden; height:100%; min-height:420px; border-radius:6px; border:1px solid rgba(255,255,255,0.12);">
        <img src="/assets/img/studio/bike-detailing.png" alt="Superbike Detailing Profile at The DDC" style="width:100%; height:100%; object-fit:cover; display:block;">
      </div>
      <div class="bike-mosaic-stacked">
        <div style="overflow:hidden; height:200px; border-radius:6px; border:1px solid rgba(255,255,255,0.12);">
          <img src="/assets/img/bike/superbike-detailing-india.jpg" alt="Motorcycle Engine & Exhaust Detail" style="width:100%; height:100%; object-fit:cover; display:block;">
        </div>
        <div style="overflow:hidden; height:200px; border-radius:6px; border:1px solid rgba(255,255,255,0.12);">
          <img src="/assets/img/studio/workshop-studio.png" alt="Studio Workshop Detailing Bay" style="width:100%; height:100%; object-fit:cover; display:block;">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================
     07–09. WASH TRIO (IMAGE-HEAVY)
     ======================================================================== -->
<section class="section section-grey">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">STUDIO WASH PROGRAMS</div>
    <h2 class="h2-section" style="margin-bottom: 48px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">PRECISION WASH</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">TRIO.</span></span>
    </h2>

    <div class="grid-3-col">
      <!-- Wash 1: Ceramic Wash -->
      <div class="wash-trio-card" data-anim="fade-up">
        <div class="wash-trio-img-wrap">
          <img src="/assets/img/studio/ceramic-wash.png" alt="Ceramic Wash Foam Bath at The DDC" class="wash-trio-img">
        </div>
        <div class="wash-trio-body">
          <div>
            <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">
              CERAMIC WASH
            </div>
            <h3 class="h3-title" style="font-size:28px; margin-bottom:12px;">CLEAN. GLOSS. DRIVE.</h3>
            <p style="color:var(--text-2); font-size:14px; line-height:1.6; margin-bottom:24px;">
              SiO2 ceramic infused shampoo bath that encapsulates dirt while leaving an intense hydrophobic layer.
            </p>
          </div>
          <a href="/services/ceramic-wash" class="text-link">EXPLORE CERAMIC WASH →</a>
        </div>
      </div>

      <!-- Wash 2: Steam Wash -->
      <div class="wash-trio-card" data-anim="fade-up" style="transition-delay: 0.1s;">
        <div class="wash-trio-img-wrap">
          <img src="/assets/img/studio/steam-wash.png" alt="High Pressure Thermal Steam Wash at The DDC" class="wash-trio-img">
        </div>
        <div class="wash-trio-body">
          <div>
            <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">
              STEAM WASH
            </div>
            <h3 class="h3-title" style="font-size:28px; margin-bottom:12px;">DEEP CLEAN. PRECISE CARE.</h3>
            <p style="color:var(--text-2); font-size:14px; line-height:1.6; margin-bottom:24px;">
              High-heat thermal vapor steam sanitizes AC vents, dissolves stubborn grease, and eliminates 99.9% of bacteria.
            </p>
          </div>
          <a href="/services/steam-wash" class="text-link">EXPLORE STEAM WASH →</a>
        </div>
      </div>

      <!-- Wash 3: Basic Car Wash -->
      <div class="wash-trio-card" data-anim="fade-up" style="transition-delay: 0.2s;">
        <div class="wash-trio-img-wrap">
          <img src="/assets/img/studio/basic-wash.png" alt="Basic Car Wash Express Care at The DDC" class="wash-trio-img">
        </div>
        <div class="wash-trio-body">
          <div>
            <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">
              BASIC CAR WASH
            </div>
            <h3 class="h3-title" style="font-size:28px; margin-bottom:12px;">CLEAN INSIDE & OUT.</h3>
            <p style="color:var(--text-2); font-size:14px; line-height:1.6; margin-bottom:24px;">
              Thorough scratch-free dual-bucket hand wash, wheel face clean, and complete interior cabin vacuuming.
            </p>
          </div>
          <a href="/services/basic-car-wash" class="text-link">EXPLORE BASIC CAR WASH →</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================
     10. 25-MINUTE SIGNATURE WASH (WHITE)
     ======================================================================== -->
<section class="section section-white signature-wash-section">
  <div class="container" style="text-align:center;">
    <div class="eyebrow" style="justify-content:center;" data-anim="fade-up">SIGNATURE EFFICIENCY</div>
    <h2 class="h2-section" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">25 MINUTES.</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">ONE CLEAN RIDE.</span></span>
    </h2>

    <div class="wash-timer-display" data-anim="fade-up">00:25:00</div>

    <!-- 7-Step ScrollTrigger Timeline -->
    <div class="signature-wash-timeline">
      <div class="timeline-track">
        <div class="timeline-progress-bar"></div>
      </div>
      <div class="timeline-steps-grid">
        <div class="timeline-step-node">
          <div class="timeline-step-dot"></div>
          <div class="timeline-step-title">01 / PRE-WASH</div>
        </div>
        <div class="timeline-step-node">
          <div class="timeline-step-dot"></div>
          <div class="timeline-step-title">02 / WHEELS & TYRES</div>
        </div>
        <div class="timeline-step-node">
          <div class="timeline-step-dot"></div>
          <div class="timeline-step-title">03 / HAND WASH</div>
        </div>
        <div class="timeline-step-node">
          <div class="timeline-step-dot"></div>
          <div class="timeline-step-title">04 / GLASS CLEAN</div>
        </div>
        <div class="timeline-step-node">
          <div class="timeline-step-dot"></div>
          <div class="timeline-step-title">05 / TYRE DRESSING</div>
        </div>
        <div class="timeline-step-node">
          <div class="timeline-step-dot"></div>
          <div class="timeline-step-title">06 / FINAL WIPE</div>
        </div>
        <div class="timeline-step-node">
          <div class="timeline-step-dot"></div>
          <div class="timeline-step-title">07 / READY</div>
        </div>
      </div>
    </div>

    <!-- Mandatory Disclaimer -->
    <p class="text-muted" style="font-size:13px; font-family:monospace; margin-top:32px;">
      * Disclaimer: Applies to eligible wash services only. PPF, ceramic coating and multi-stage detailing take longer.
    </p>
  </div>
</section>

<!-- ========================================================================
     11. OTHER SERVICES (GREY)
     ======================================================================== -->
<section class="section section-grey">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">SPECIALIZED TREATMENTS</div>
    <h2 class="h2-section" style="margin-bottom: 48px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">ADDITIONAL</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">SOLUTIONS.</span></span>
    </h2>

    <div class="other-services-grid">
      <!-- 2x2 Feature Card: Ceramic Coating -->
      <div class="other-service-card card-feature" data-anim="fade-up">
        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:12px;">
            01 / CERAMIC PROTECTION
          </div>
          <h3 class="h3-title" style="font-size: clamp(36px, 4vw, 56px); margin-bottom: 20px;">
            CERAMIC COATING
          </h3>
          <p style="color:var(--grey); font-size:16px; line-height:1.7; max-width:480px;">
            Permanent 9H nano-glass matrix delivering candy optical gloss, extreme water beading, and UV oxidation resistance for cars and bikes.
          </p>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-top:40px;">
          <a href="/services/ceramic-coating" class="btn btn-red">EXPLORE CERAMIC COATING →</a>
          <div class="card-arrow-box">→</div>
        </div>
      </div>

      <!-- Card 2: Engine Detailing -->
      <div class="other-service-card" data-anim="fade-up">
        <div>
          <div style="font-size:11px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">02 / MECHANICAL</div>
          <h4 style="font-family:var(--font-display); font-size:24px; margin-bottom:12px;">ENGINE DETAILING</h4>
          <p style="color:var(--text-2); font-size:14px; line-height:1.6;">Safe dry-steam cleaning and OEM satin dressing for under-hood components.</p>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">
          <a href="/services/engine-detailing" class="text-link">DETAILS →</a>
          <div class="card-arrow-box">→</div>
        </div>
      </div>

      <!-- Card 3: Interior Deep Clean -->
      <div class="other-service-card" data-anim="fade-up">
        <div>
          <div style="font-size:11px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">03 / CABIN</div>
          <h4 style="font-family:var(--font-display); font-size:24px; margin-bottom:12px;">INTERIOR DEEP CLEAN</h4>
          <p style="color:var(--text-2); font-size:14px; line-height:1.6;">Hot water extraction and leather conditioning for a factory-fresh cabin reset.</p>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">
          <a href="/services/interior-deep-clean" class="text-link">DETAILS →</a>
          <div class="card-arrow-box">→</div>
        </div>
      </div>

      <!-- Card 4: Polishing & Waxing -->
      <div class="other-service-card" data-anim="fade-up">
        <div>
          <div style="font-size:11px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">04 / GLOSS</div>
          <h4 style="font-family:var(--font-display); font-size:24px; margin-bottom:12px;">POLISHING & WAXING</h4>
          <p style="color:var(--text-2); font-size:14px; line-height:1.6;">Single-stage machine cut and polymer sealant to enhance paint reflection.</p>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">
          <a href="/services/polishing-waxing" class="text-link">DETAILS →</a>
          <div class="card-arrow-box">→</div>
        </div>
      </div>

      <!-- Card 5: Tyre & Rim Care -->
      <div class="other-service-card" data-anim="fade-up">
        <div>
          <div style="font-size:11px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">05 / WHEELS</div>
          <h4 style="font-family:var(--font-display); font-size:24px; margin-bottom:12px;">TYRE & RIM CARE</h4>
          <p style="color:var(--text-2); font-size:14px; line-height:1.6;">Acid-free iron fallout removal from alloy barrels and non-sling tyre nourishment.</p>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">
          <a href="/services/tyre-rim-care" class="text-link">DETAILS →</a>
          <div class="card-arrow-box">→</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================
     12. VEHICLE DIAGNOSIS (Interactive Widget)
     ======================================================================== -->
<section class="section section-light diagnosis-widget">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">TAILORED RECOMMENDATION</div>
    <h2 class="h2-section" style="margin-bottom: 16px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">WHAT ARE YOU</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">LOOKING TO IMPROVE?</span></span>
    </h2>
    <p class="lead-text" style="margin-bottom: 40px;" data-anim="fade-up">
      Select your primary automotive goal to receive an immediate studio recommendation:
    </p>

    <!-- Options Grid -->
    <div class="diagnosis-options-grid" data-anim="fade-up">
      <button class="diagnosis-option-btn is-selected" data-key="protect-paint">Protect my paint →</button>
      <button class="diagnosis-option-btn" data-key="remove-dirt">Remove dirt / contamination →</button>
      <button class="diagnosis-option-btn" data-key="refresh-interior">Refresh interior →</button>
      <button class="diagnosis-option-btn" data-key="detail-bike">Detail my bike →</button>
      <button class="diagnosis-option-btn" data-key="basic-wash">Basic routine wash →</button>
      <button class="diagnosis-option-btn" data-key="full-transformation">Full transformation →</button>
    </div>

    <!-- Recommended Service Result Panel (Dark) -->
    <div class="diagnosis-result-panel" data-anim="fade-up">
      <div>
        <div style="font-size:12px; font-weight:700; color:var(--red-bright); letter-spacing:0.2em; text-transform:uppercase; margin-bottom:8px;">
          YOUR RECOMMENDED SERVICE
        </div>
        <div class="diagnosis-result-title" style="font-family:var(--font-display); font-size:clamp(32px, 4vw, 56px); line-height:0.95; margin-bottom:12px;">
          PAINT PROTECTION FILM (PPF)
        </div>
        <p class="diagnosis-result-why" style="color:var(--grey); font-size:15px; line-height:1.6; max-width:600px;">
          Self-healing aliphatic polyurethane creates an impenetrable armor against rock chips, scratches, and UV oxidation.
        </p>
      </div>
      <div>
        <a href="/book?service=ppf&source=diagnosis" class="btn btn-red diagnosis-cta-btn" style="padding:0 40px;">
          GET A QUOTE →
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================
     13. BEFORE / AFTER (DARK) — Hidden as requested
     ======================================================================== -->
<section class="section section-dark before-after-section" style="display:none;" aria-hidden="true">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">PROVEN RESULTS</div>
    <h2 class="h2-section" style="margin-bottom: 24px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">REAL VEHICLES.</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">REAL TRANSFORMATIONS.</span></span>
    </h2>

    <!-- Category Chips -->
    <div class="ba-chips-nav" data-anim="fade-up">
      <button class="ba-category-chip is-active" data-category="ppf">PPF</button>
      <button class="ba-category-chip" data-category="bike">BIKE</button>
      <button class="ba-category-chip" data-category="ceramic-wash">CERAMIC WASH</button>
      <button class="ba-category-chip" data-category="steam-wash">STEAM WASH</button>
      <button class="ba-category-chip" data-category="detailing">FULL DETAILING</button>
      <button class="ba-category-chip" data-category="engine">ENGINE</button>
    </div>

    <!-- Before / After Slider Container -->
    <div class="ba-pairs-stage" data-anim="mask-reveal">
      <!-- Pair 1: PPF & Paint Correction -->
      <div class="ba-pair-item" data-category="ppf">
        <div class="before-after-container">
          <!-- Before Image -->
          <img src="/assets/img/before-after/paint-before.jpg" alt="Before: Swirled and oxidized clear coat" class="before-after-media">
          <!-- After Layer -->
          <div class="before-after-layer-after">
            <img src="/assets/img/before-after/paint-after.jpg" alt="After: Flawless self-healing PPF protected finish" class="before-after-media">
          </div>
          <!-- Center Divider & Red Knob -->
          <div class="before-after-divider">
            <div class="before-after-knob">DRAG</div>
          </div>
          <!-- Full-size Input Range -->
          <input type="range" min="0" max="100" value="50" class="before-after-input" aria-label="Slide to compare before and after transformation">
        </div>
      </div>

      <!-- Pair 2: Bike -->
      <div class="ba-pair-item" data-category="bike" style="display:none;">
        <div class="before-after-container">
          <img src="/assets/img/bike/bike-2.jpg" alt="Before: Grimy motorcycle exhaust and engine" class="before-after-media">
          <div class="before-after-layer-after">
            <img src="/assets/img/bike/superbike-detailing-india.jpg" alt="After: Precision detailed superbike finish" class="before-after-media">
          </div>
          <div class="before-after-divider">
            <div class="before-after-knob">DRAG</div>
          </div>
          <input type="range" min="0" max="100" value="50" class="before-after-input" aria-label="Slide to compare before and after motorcycle detailing">
        </div>
      </div>

      <!-- Pair 3: Ceramic Wash -->
      <div class="ba-pair-item" data-category="ceramic-wash" style="display:none;">
        <div class="before-after-container">
          <img src="/assets/img/before-after/paint-before.jpg" alt="Before: Road grime and dirt on clear coat" class="before-after-media">
          <div class="before-after-layer-after">
            <img src="/assets/img/wash/ceramic-wash.jpg" alt="After: Hydrophobic SiO2 foam wash reflection" class="before-after-media">
          </div>
          <div class="before-after-divider">
            <div class="before-after-knob">DRAG</div>
          </div>
          <input type="range" min="0" max="100" value="50" class="before-after-input" aria-label="Slide to compare before and after ceramic wash">
        </div>
      </div>

      <!-- Pair 4: Full Detailing & Interior -->
      <div class="ba-pair-item" data-category="detailing" style="display:none;">
        <div class="before-after-container">
          <img src="/assets/img/before-after/interior-before.jpg" alt="Before: Soiled cabin upholstery" class="before-after-media">
          <div class="before-after-layer-after">
            <img src="/assets/img/before-after/interior-after.jpg" alt="After: Sanitized and conditioned leather cockpit" class="before-after-media">
          </div>
          <div class="before-after-divider">
            <div class="before-after-knob">DRAG</div>
          </div>
          <input type="range" min="0" max="100" value="50" class="before-after-input" aria-label="Slide to compare before and after interior detailing">
        </div>
      </div>

      <!-- Empty State Fallback -->
      <div class="ba-empty-state" style="display:none; padding:80px 20px; text-align:center; color:var(--text-3); font-family:monospace;">
        [ NO TRANSFORMATIONS RECORDED FOR THIS CATEGORY YET ]
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================
     15. BUILT FOR JAMMU (WHITE)
     ======================================================================== -->
<section class="section section-white">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">ENVIRONMENTAL CALIBRATION</div>
    <h2 class="h2-section" style="margin-bottom: 20px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">BUILT FOR</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">JAMMU ROADS.</span></span>
    </h2>
    <p class="lead-text" style="margin-bottom: 48px; max-width: 800px;" data-anim="fade-up">
      Dust. Heat. Rain. City traffic. Highway runs. Your vehicle deals with all of it.
    </p>

    <div class="built-jammu-grid" data-anim="fade-up">
      <div class="built-jammu-card">
        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">01 / HIGHWAY RUNS</div>
          <h4 style="font-size:24px; font-family:var(--font-display); margin-bottom:12px;">PPF SHIELD</h4>
          <p style="font-size:14px; color:var(--text-2); line-height:1.6;">Shields against Jammu-Srinagar and Jammu-Delhi highway stone chips.</p>
        </div>
        <a href="/services/ppf" class="text-link" style="margin-top:16px;">EXPLORE PPF →</a>
      </div>

      <div class="built-jammu-card">
        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">02 / CITY DUST</div>
          <h4 style="font-size:24px; font-family:var(--font-display); margin-bottom:12px;">BIKE CARE</h4>
          <p style="font-size:14px; color:var(--text-2); line-height:1.6;">Purges fine dust from engine cooling fins and chain sprockets.</p>
        </div>
        <a href="/services/bike-detailing" class="text-link" style="margin-top:16px;">BIKE CARE →</a>
      </div>

      <div class="built-jammu-card">
        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">03 / CITY TRAFFIC</div>
          <h4 style="font-size:24px; font-family:var(--font-display); margin-bottom:12px;">CERAMIC WASH</h4>
          <p style="font-size:14px; color:var(--text-2); line-height:1.6;">Removes exhaust soot and urban grime without inducing swirl marks.</p>
        </div>
        <a href="/services/ceramic-wash" class="text-link" style="margin-top:16px;">CERAMIC WASH →</a>
      </div>

      <div class="built-jammu-card">
        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">04 / MONSOON RAIN</div>
          <h4 style="font-size:24px; font-family:var(--font-display); margin-bottom:12px;">STEAM WASH</h4>
          <p style="font-size:14px; color:var(--text-2); line-height:1.6;">Eliminates damp footwear odors and sterilizes AC ventilation lines.</p>
        </div>
        <a href="/services/steam-wash" class="text-link" style="margin-top:16px;">STEAM WASH →</a>
      </div>

      <div class="built-jammu-card">
        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:8px;">05 / ROUTINE CARE</div>
          <h4 style="font-size:24px; font-family:var(--font-display); margin-bottom:12px;">BASIC CAR WASH</h4>
          <p style="font-size:14px; color:var(--text-2); line-height:1.6;">Essential scratch-free dual-bucket wash and interior vacuum for routine weekly driving.</p>
        </div>
        <a href="/services/basic-car-wash" class="text-link" style="margin-top:16px;">BASIC CAR WASH →</a>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================
     16. WHY THE DRIVE CLINIC (LIGHT)
     ======================================================================== -->
<section class="section section-light">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">THE DRIVE CLINIC DIFFERENCE</div>
    <h2 class="h2-section" style="margin-bottom: 48px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">WHY CHOOSE</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">THE DRIVE CLINIC?</span></span>
    </h2>

    <div class="grid-3-col">
      <!-- Block 1 -->
      <div class="why-card" data-anim="fade-up">
        <div>
          <div class="why-number">01</div>
          <h3 class="h3-title" style="font-size:32px; margin-bottom:16px;">PREMIUM PRODUCTS</h3>
          <p style="color:var(--text-2); font-size:15px; line-height:1.7;">
            We exclusively formulate and apply certified pH-neutral chemicals, genuine SiO2 ceramic coatings, and high-tensile aliphatic polyurethane films that do not compromise factory paint.
          </p>
        </div>
        <div style="margin-top:24px;">
          <div class="redline" style="width:48px;"></div>
        </div>
      </div>

      <!-- Block 2 -->
      <div class="why-card" data-anim="fade-up" style="transition-delay: 0.1s;">
        <div>
          <div class="why-number">02</div>
          <h3 class="h3-title" style="font-size:32px; margin-bottom:16px;">EXPERT TEAM</h3>
          <p style="color:var(--text-2); font-size:15px; line-height:1.7;">
            Our studio technicians are trained in advanced rotary paint correction, dust-free PPF edge wrapping, and delicate interior steam extraction to ensure zero vehicle damage.
          </p>
        </div>
        <div style="margin-top:24px;">
          <div class="redline" style="width:48px;"></div>
        </div>
      </div>

      <!-- Block 3 -->
      <div class="why-card" data-anim="fade-up" style="transition-delay: 0.2s;">
        <div>
          <div class="why-number">03</div>
          <h3 class="h3-title" style="font-size:32px; margin-bottom:16px;">TOP QUALITY BAYS</h3>
          <p style="color:var(--text-2); font-size:15px; line-height:1.7;">
            Our Nanak Nagar facility features high-CRI inspection lighting arrays, de-ionized spot-free water filtration, and enclosed clean bays for unmatched installation clarity.
          </p>
        </div>
        <div style="margin-top:24px;">
          <div class="redline" style="width:48px;"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================
     17. CUSTOMER JOURNEY (GREY)
     ======================================================================== -->
<section class="section section-grey">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">SEAMLESS WORKFLOW</div>
    <h2 class="h2-section" style="margin-bottom: 48px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">YOUR DETAILED</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">JOURNEY.</span></span>
    </h2>

    <div class="redline" style="margin-bottom: 24px;" data-anim="redline"></div>

    <div class="journey-timeline-grid" data-anim="fade-up">
      <div class="journey-step-card">
        <div class="journey-step-dot"></div>
        <div style="font-size:12px; font-weight:700; color:var(--red); margin-bottom:6px;">STEP 01</div>
        <h4 style="font-size:18px; font-family:var(--font-display); margin-bottom:8px;">BOOK ONLINE</h4>
        <p style="font-size:13px; color:var(--text-2); line-height:1.5;">Pick your service & preferred slot in under 2 minutes.</p>
      </div>

      <div class="journey-step-card">
        <div class="journey-step-dot"></div>
        <div style="font-size:12px; font-weight:700; color:var(--red); margin-bottom:6px;">STEP 02</div>
        <h4 style="font-size:18px; font-family:var(--font-display); margin-bottom:8px;">WHATSAPP CONFIRM</h4>
        <p style="font-size:13px; color:var(--text-2); line-height:1.5;">We confirm appointment and slot details directly.</p>
      </div>

      <div class="journey-step-card">
        <div class="journey-step-dot"></div>
        <div style="font-size:12px; font-weight:700; color:var(--red); margin-bottom:6px;">STEP 03</div>
        <h4 style="font-size:18px; font-family:var(--font-display); margin-bottom:8px;">VEHICLE RECEIVED</h4>
        <p style="font-size:13px; color:var(--text-2); line-height:1.5;">Drop-off and initial paint inspection log.</p>
      </div>

      <div class="journey-step-card">
        <div class="journey-step-dot"></div>
        <div style="font-size:12px; font-weight:700; color:var(--red); margin-bottom:6px;">STEP 04</div>
        <h4 style="font-size:18px; font-family:var(--font-display); margin-bottom:8px;">IN SERVICE</h4>
        <p style="font-size:13px; color:var(--text-2); line-height:1.5;">Precision detailing in our dedicated clean bays.</p>
      </div>

      <div class="journey-step-card">
        <div class="journey-step-dot"></div>
        <div style="font-size:12px; font-weight:700; color:var(--red); margin-bottom:6px;">STEP 05</div>
        <h4 style="font-size:18px; font-family:var(--font-display); margin-bottom:8px;">QUALITY AUDIT</h4>
        <p style="font-size:13px; color:var(--text-2); line-height:1.5;">High-intensity lighting check across all panels.</p>
      </div>

      <div class="journey-step-card">
        <div class="journey-step-dot"></div>
        <div style="font-size:12px; font-weight:700; color:var(--red); margin-bottom:6px;">STEP 06</div>
        <h4 style="font-size:18px; font-family:var(--font-display); margin-bottom:8px;">READY TO DRIVE</h4>
        <p style="font-size:13px; color:var(--text-2); line-height:1.5;">Handover with care tips and warranty registration.</p>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================
     18. REVIEWS (WHITE) — Genuine Reviews Only
     ======================================================================== -->
<section class="section section-white">
  <div class="container">
    <div class="eyebrow" data-anim="fade-up">CLIENT EXPERIENCES</div>
    <h2 class="h2-section" style="margin-bottom: 48px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">VERIFIED</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">FEEDBACK.</span></span>
    </h2>

    <div class="grid-3-col" data-anim="fade-up">
      <!-- Skeleton Placeholder Cards -->
      <div style="background:var(--off); border:1px solid var(--line); padding:32px; min-height:220px; display:flex; flex-direction:column; justify-content:space-between;">
        <div>
          <div style="color:var(--red); font-size:18px; margin-bottom:12px;">★★★★★</div>
          <p style="color:var(--text-2); font-size:14px; line-height:1.6; font-style:italic;">
            "Real Google reviews from verified studio clients will appear dynamically here."
          </p>
        </div>
        <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:var(--text-3); margin-top:20px;">
          VERIFIED GOOGLE REVIEW
        </div>
      </div>

      <div style="background:var(--off); border:1px solid var(--line); padding:32px; min-height:220px; display:flex; flex-direction:column; justify-content:space-between;">
        <div>
          <div style="color:var(--red); font-size:18px; margin-bottom:12px;">★★★★★</div>
          <p style="color:var(--text-2); font-size:14px; line-height:1.6; font-style:italic;">
            "Real Google reviews from verified studio clients will appear dynamically here."
          </p>
        </div>
        <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:var(--text-3); margin-top:20px;">
          VERIFIED GOOGLE REVIEW
        </div>
      </div>

      <div style="background:var(--off); border:1px solid var(--line); padding:32px; min-height:220px; display:flex; flex-direction:column; justify-content:space-between;">
        <div>
          <div style="color:var(--red); font-size:18px; margin-bottom:12px;">★★★★★</div>
          <p style="color:var(--text-2); font-size:14px; line-height:1.6; font-style:italic;">
            "Real Google reviews from verified studio clients will appear dynamically here."
          </p>
        </div>
        <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:var(--text-3); margin-top:20px;">
          VERIFIED GOOGLE REVIEW
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================
     19. LOCATION & STUDIO HOURS (LIGHT)
     ======================================================================== -->
<section class="section section-light">
  <div class="container grid-5-7">
    <div>
      <div class="eyebrow" data-anim="fade-up">VISIT OUR STUDIO</div>
      <h2 class="h2-section" data-anim="text-reveal">
        <span class="anim-text-line"><span class="anim-text-inner">NANAK NAGAR,</span></span>
        <span class="anim-text-line"><span class="anim-text-inner text-red">JAMMU.</span></span>
      </h2>

      <div style="display:flex; flex-direction:column; gap:24px; margin: 32px 0 40px;" data-anim="fade-up">
        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:6px;">ADDRESS</div>
          <p style="font-size:16px; color:var(--ink); line-height:1.6;">
            <?= htmlspecialchars($settings['address'] ?? 'Nanak Nagar, Jammu, Jammu & Kashmir 180004, India') ?>
          </p>
        </div>

        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:6px;">PHONE & WHATSAPP</div>
          <p style="font-size:16px; color:var(--ink);">
            <a href="tel:<?= htmlspecialchars($settings['phone'] ?? '+91 94191 00000') ?>" style="font-weight:700;"><?= htmlspecialchars($settings['phone'] ?? '+91 94191 00000') ?></a> • 
            <a href="https://wa.me/<?= htmlspecialchars($settings['whatsapp'] ?? '919419100000') ?>" target="_blank" rel="noopener" class="text-link">WhatsApp →</a>
          </p>
        </div>

        <div>
          <div style="font-size:12px; font-weight:700; color:var(--red); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:6px;">OPERATING HOURS</div>
          <p style="font-size:15px; color:var(--text-2);">
            <?= htmlspecialchars($settings['hours'] ?? 'Monday – Sunday: 09:00 AM – 08:00 PM (Open 7 Days)') ?>
          </p>
        </div>
      </div>

      <div style="display:flex; gap:16px; flex-wrap:wrap;" data-anim="fade-up">
        <a href="https://maps.google.com/?q=The+Drive+Clinic+Nanak+Nagar+Jammu" target="_blank" rel="noopener" class="btn btn-red">GET DIRECTIONS →</a>
        <a href="/book" class="btn btn-ghost-dark">BOOK A SERVICE →</a>
      </div>
    </div>

    <!-- Lazy Google Map Embed -->
    <div>
      <div style="border:1px solid var(--line); overflow:hidden; min-height:420px; background:#151A26;" data-anim="mask-reveal">
        <iframe 
          src="<?= htmlspecialchars($settings['map_embed_url'] ?? 'https://maps.google.com') ?>" 
          width="100%" 
          height="420" 
          style="border:0; display:block;" 
          allowfullscreen="" 
          loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade"
          title="The Drive Clinic Google Map Location">
        </iframe>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================
     20. FINAL CTA (DARK)
     ======================================================================== -->
<section class="section section-dark" style="position:relative; overflow:hidden;">
  <div class="container" style="position:relative; z-index:2; text-align:center; padding-top:40px; padding-bottom:40px;">
    <div class="eyebrow" style="justify-content:center;" data-anim="fade-up">EXPERIENCE TRUE DETAILING</div>
    <h2 class="h2-section" style="font-size:clamp(64px, 9vw, 140px); margin-bottom:24px;" data-anim="text-reveal">
      <span class="anim-text-line"><span class="anim-text-inner">READY TO</span></span>
      <span class="anim-text-line"><span class="anim-text-inner">REDEFINE</span></span>
      <span class="anim-text-line"><span class="anim-text-inner text-red">YOUR RIDE?</span></span>
    </h2>
    <div class="redline" style="max-width:140px; margin: 0 auto 32px;" data-anim="redline"></div>
    <p class="lead-text" style="color:var(--grey); max-width:640px; margin: 0 auto 48px;" data-anim="fade-up">
      Book your next detailing experience or studio wash at The Drive Clinic in Nanak Nagar, Jammu.
    </p>

    <div style="display:flex; justify-content:center; gap:20px; flex-wrap:wrap;" data-anim="fade-up">
      <a href="/book" class="btn btn-red" style="padding: 0 44px;" data-cursor="BOOK">
        BOOK YOUR SERVICE →
      </a>
      <a href="https://wa.me/<?= htmlspecialchars($settings['whatsapp'] ?? '919419100000') ?>" target="_blank" rel="noopener" class="btn btn-ghost" data-cursor="CHAT">
        WHATSAPP US →
      </a>
    </div>
  </div>
</section>
