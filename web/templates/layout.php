<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Global Layout Shell
$pageTitle = $pageTitle ?? 'The Drive Clinic | Premium Detailing & PPF Studio in Jammu';
$metaDesc = $metaDesc ?? 'The Drive Clinic is Jammu\'s premier automotive detailing studio for PPF, ceramic coating, motorcycle detailing, and premium wash services.';
$siteUrl = rtrim($config['site_url'] ?? 'http://localhost:8000', '/');
$currentUrl = $siteUrl . ($_SERVER['REQUEST_URI'] ?? '/');
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
  <link rel="canonical" href="<?= htmlspecialchars($currentUrl) ?>">

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= htmlspecialchars($currentUrl) ?>">
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($metaDesc) ?>">
  <meta property="og:image" content="<?= $siteUrl ?>/assets/img/hero/slide-1-desktop.jpg">

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:url" content="<?= htmlspecialchars($currentUrl) ?>">
  <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($metaDesc) ?>">
  <meta name="twitter:image" content="<?= $siteUrl ?>/assets/img/hero/slide-1-desktop.jpg">

  <!-- Preconnect & Google Fonts (Bebas Neue + Space Grotesk) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="/assets/brand/favicon.svg">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="/assets/css/main.css">

  <!-- Schema.org JSON-LD LocalBusiness -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "AutoRepair",
    "name": "THE DRIVE CLINIC",
    "image": "<?= $siteUrl ?>/assets/img/hero/slide-1-desktop.jpg",
    "description": "<?= htmlspecialchars($metaDesc) ?>",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Nanak Nagar",
      "addressLocality": "Jammu",
      "addressRegion": "Jammu and Kashmir",
      "postalCode": "180004",
      "addressCountry": "IN"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": "32.701234",
      "longitude": "74.871234"
    },
    "url": "<?= $siteUrl ?>",
    "telephone": "<?= htmlspecialchars($settings['phone'] ?? '+91 94191 00000') ?>",
    "priceRange": "$$",
    "openingHoursSpecification": [
      {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
        "opens": "09:00",
        "closes": "20:00"
      }
    ]
  }
  </script>
</head>
<body id="top">
  <!-- Accessibility Skip Link -->
  <a href="#main-content" class="skip-link">Skip to main content</a>

  <!-- Global Header / Navigation -->
  <?php require __DIR__ . '/partials/navbar.php'; ?>

  <!-- Main Content Slot -->
  <main id="main-content">
    <?php require $viewPath; ?>
  </main>

  <!-- Global Footer -->
  <?php require __DIR__ . '/partials/footer.php'; ?>

  <!-- Sticky Mobile Bottom Bar (≤760px) -->
  <?php require __DIR__ . '/partials/mobile-bar.php'; ?>

  <!-- External Libraries (Pinned CDNs) -->
  <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.9/dist/lenis.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>

  <!-- Hand-Authored ES Modules -->
  <script type="module" src="/assets/js/main.js"></script>
</body>
</html>
