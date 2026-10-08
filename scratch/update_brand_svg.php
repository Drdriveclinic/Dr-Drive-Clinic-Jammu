<?php
$pngPath = dirname(__DIR__) . '/web/public/assets/brand/logo-official.png';
$b64 = base64_encode(file_get_contents($pngPath));

$svgContent = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 873 297" width="873" height="297">
  <image href="data:image/png;base64,{$b64}" width="873" height="297" />
</svg>
SVG;

file_put_contents(dirname(__DIR__) . '/web/public/assets/brand/logo-white.svg', $svgContent);
file_put_contents(dirname(__DIR__) . '/web/public/assets/brand/logo-dark.svg', $svgContent);
file_put_contents(dirname(__DIR__) . '/web/public/assets/brand/logo-official.svg', $svgContent);
copy($pngPath, dirname(__DIR__) . '/web/public/assets/brand/logo-white.png');
copy($pngPath, dirname(__DIR__) . '/web/public/assets/brand/logo.png');

echo "SVGs & PNGs updated with official cutout logo!\n";
