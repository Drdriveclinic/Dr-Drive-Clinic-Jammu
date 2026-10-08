<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Reusable FAQ Accordion Component
$faqList = $faqList ?? [];
if (empty($faqList)) return;
?>
<div class="faq-accordion" data-anim="fade-up">
  <?php foreach ($faqList as $fIdx => $faq): ?>
    <div class="faq-item">
      <button class="faq-trigger" aria-expanded="false" aria-controls="faq-ans-<?= $faq['id'] ?>" id="faq-btn-<?= $faq['id'] ?>">
        <span><?= htmlspecialchars($faq['question']) ?></span>
        <span class="faq-icon" aria-hidden="true">+</span>
      </button>
      <div class="faq-content" id="faq-ans-<?= $faq['id'] ?>" role="region" aria-labelledby="faq-btn-<?= $faq['id'] ?>">
        <div class="faq-body">
          <?= nl2br(htmlspecialchars($faq['answer'])) ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Output FAQPage JSON-LD ONLY for Answered FAQs -->
<?php
$answeredFaqs = array_filter($faqList, fn($f) => !empty($f['is_answered']));
if (!empty($answeredFaqs)):
  $faqSchema = [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => array_map(function($f) {
      return [
        '@type'          => 'Question',
        'name'           => $f['question'],
        'acceptedAnswer' => [
          '@type' => 'Answer',
          'text'  => $f['answer']
        ]
      ];
    }, array_values($answeredFaqs))
  ];
?>
  <script type="application/ld+json">
  <?= json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
  </script>
<?php endif; ?>
