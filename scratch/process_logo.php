<?php
// Process user logo: remove black background, boost contrast, make smooth transparent PNG

$srcPath = 'C:\\Users\\Asus\\.gemini\\antigravity-ide\\brain\\72f80b42-515a-43a1-9ca0-6438a7feb556\\.user_uploaded\\media_1791474988730.png';
$destPng = dirname(__DIR__) . '/web/public/assets/brand/logo-official.png';
$destPngWhite = dirname(__DIR__) . '/web/public/assets/brand/logo-transparent.png';

$src = imagecreatefrompng($srcPath);
$w = imagesx($src);
$h = imagesy($src);

// Find bounding box of actual logo content to trim excess black padding
$minX = $w; $minY = $h; $maxX = 0; $maxY = 0;

for ($y = 0; $y < $h; $y++) {
    for ($x = 0; $x < $w; $x++) {
        $rgb = imagecolorat($src, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $brightness = max($r, $g, $b);
        if ($brightness > 28) {
            if ($x < $minX) $minX = $x;
            if ($x > $maxX) $maxX = $x;
            if ($y < $minY) $minY = $y;
            if ($y > $maxY) $maxY = $y;
        }
    }
}

$padding = 10;
$cropX = max(0, $minX - $padding);
$cropY = max(0, $minY - $padding);
$cropW = min($w - $cropX, ($maxX - $minX) + ($padding * 2));
$cropH = min($h - $cropY, ($maxY - $minY) + ($padding * 2));

$cropped = imagecreatetruecolor($cropW, $cropH);
imagealphablending($cropped, false);
imagesavealpha($cropped, true);

// Create transparent destination image with alpha gradient keyed from black
for ($y = 0; $y < $cropH; $y++) {
    for ($x = 0; $x < $cropW; $x++) {
        $origX = $cropX + $x;
        $origY = $cropY + $y;
        $rgb = imagecolorat($src, $origX, $origY);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        
        $maxVal = max($r, $g, $b);
        
        if ($maxVal <= 18) {
            // Completely transparent background
            $col = imagecolorallocatealpha($cropped, 0, 0, 0, 127);
        } else {
            // Calculate smooth alpha anti-aliasing
            $alphaRatio = min(1.0, ($maxVal - 18) / 75.0);
            $gdAlpha = (int)round((1.0 - $alphaRatio) * 127);
            
            // Normalize color so black fringing is eliminated
            $normFactor = $maxVal > 0 ? (255 / $maxVal) : 1;
            // Retain original red/white chromatic balance
            $nr = min(255, (int)round($r * $normFactor));
            $ng = min(255, (int)round($g * $normFactor));
            $nb = min(255, (int)round($b * $normFactor));
            
            // If it's red (CLINIC or red car stroke)
            if ($r > $g * 1.6 && $r > $b * 1.6) {
                $nr = 229;
                $ng = 9;
                $nb = 20;
            } elseif ($maxVal > 160) {
                // White text / silver car lines
                $nr = 255;
                $ng = 255;
                $nb = 255;
            }
            
            $col = imagecolorallocatealpha($cropped, $nr, $ng, $nb, $gdAlpha);
        }
        imagesetpixel($cropped, $x, $y, $col);
    }
}

imagepng($cropped, $destPng, 9);
imagepng($cropped, $destPngWhite, 9);
imagedestroy($src);
imagedestroy($cropped);

echo "Cutout logo successfully generated at:\n$destPng\n$destPngWhite\nSize: {$cropW}x{$cropH}\n";
