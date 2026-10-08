<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Global Dark CTA Band
$ctaBandTitle = $ctaBandTitle ?? "READY TO REDEFINE YOUR RIDE?";
$ctaBandLead = $ctaBandLead ?? "Book your detailing experience or schedule a studio inspection in Nanak Nagar, Jammu.";
$ctaBandPrimaryText = $ctaBandPrimaryText ?? "BOOK YOUR SERVICE →";
$ctaBandPrimaryHref = $ctaBandPrimaryHref ?? "/book";
$ctaBandSecondaryText = $ctaBandSecondaryText ?? "WHATSAPP US →";
$ctaBandSecondaryHref = $ctaBandSecondaryHref ?? "https://wa.me/" . ($settings['whatsapp'] ?? '919419100000');
?>
<section class="section section-dark" style="position:relative; overflow:hidden; text-align:center;">
  <div class="container" style="position:relative; z-index:2; padding-top:20px; padding-bottom:20px;">
    <div class="eyebrow" style="justify-content:center;" data-anim="fade-up">REDEFINE YOUR VEHICLE</div>
    <h2 class="h2-section" style="font-size:clamp(54px, 8vw, 110px); margin-bottom:24px;" data-anim="text-reveal">
      <?= htmlspecialchars($ctaBandTitle) ?>
    </h2>
    <div class="redline" style="max-width:140px; margin: 0 auto 32px;" data-anim="redline"></div>
    <p class="lead-text" style="color:var(--grey); max-width:640px; margin: 0 auto 40px;" data-anim="fade-up">
      <?= htmlspecialchars($ctaBandLead) ?>
    </p>
    <div style="display:flex; justify-content:center; gap:20px; flex-wrap:wrap;" data-anim="fade-up">
      <a href="<?= htmlspecialchars($ctaBandPrimaryHref) ?>" class="btn btn-red" data-cursor="BOOK">
        <?= htmlspecialchars($ctaBandPrimaryText) ?>
      </a>
      <a href="<?= htmlspecialchars($ctaBandSecondaryHref) ?>" target="_blank" rel="noopener" class="btn btn-ghost" data-cursor="CHAT">
        <?= htmlspecialchars($ctaBandSecondaryText) ?>
      </a>
    </div>
  </div>
</section>
