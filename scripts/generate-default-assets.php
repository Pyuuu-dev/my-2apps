<?php

/**
 * generate-default-assets.php
 *
 * Generates static fallback branding assets in public/:
 *   - favicon-default.svg, favicon-default.ico
 *   - favicon-default-32.png, apple-touch-icon-default.png
 *   - icon-default-192.png, icon-default-512.png
 *   - og-image-default.png (1200x630)
 *
 * Usage: php scripts/generate-default-assets.php
 */

const PUBLIC_DIR = __DIR__ . '/../public';
const BRAND_INDIGO = [99, 102, 241];   // #6366f1
const BRAND_BG = [2, 6, 23];           // #020617
const BRAND_BG2 = [15, 23, 42];        // #0f172a
const BRAND_PURPLE = [139, 92, 246];   // #8b5cf6
const FONT = '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf';
const FONT_REGULAR = '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf';

function fail(string $m): never { fwrite(STDERR, "ERROR: $m\n"); exit(1); }

if (!extension_loaded('gd')) fail('GD not available');
if (!is_file(FONT)) fail('Liberation Sans Bold not found at ' . FONT);

// ---------- 1. SVG fallback ----------
$svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
  <rect width="64" height="64" rx="14" fill="#6366f1"/>
  <path fill="#fff" d="M37 21l-22 30h12l-2 14 22-30h-12z"/>
</svg>
SVG;
file_put_contents(PUBLIC_DIR . '/favicon-default.svg', $svg);
echo "wrote favicon-default.svg\n";

// ---------- 2. Helper: draw lightning logo on canvas ----------
function drawLogoBox($size): \GdImage {
    $im = imagecreatetruecolor($size, $size);
    imagealphablending($im, false);
    imagesavealpha($im, true);
    $transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
    imagefilledrectangle($im, 0, 0, $size, $size, $transparent);
    imagealphablending($im, true);

    $indigo = imagecolorallocate($im, BRAND_INDIGO[0], BRAND_INDIGO[1], BRAND_INDIGO[2]);
    $white = imagecolorallocate($im, 255, 255, 255);

    // Rounded square (4 ellipse corners + 2 rects)
    $r = (int) ($size * 0.22);
    $d = $r * 2;
    imagefilledrectangle($im, $r, 0, $size - $r, $size, $indigo);
    imagefilledrectangle($im, 0, $r, $size, $size - $r, $indigo);
    imagefilledellipse($im, $r, $r, $d, $d, $indigo);
    imagefilledellipse($im, $size - $r, $r, $d, $d, $indigo);
    imagefilledellipse($im, $r, $size - $r, $d, $d, $indigo);
    imagefilledellipse($im, $size - $r, $size - $r, $d, $d, $indigo);

    // Lightning bolt polygon (scaled from path d="M13 10V3L4 14h7v7l9-11h-7z")
    // Using viewBox 24x24 -> scale to size
    $s = $size / 24.0;
    $pts = [
        13*$s, 10*$s,
        13*$s,  3*$s,
         4*$s, 14*$s,
        11*$s, 14*$s,
        11*$s, 21*$s,
        20*$s, 10*$s,
        13*$s, 10*$s,
    ];
    $pts = array_map('intval', $pts);
    imagefilledpolygon($im, $pts, $white);

    return $im;
}

// ---------- 3. Generate PNG variants ----------
$pngTargets = [
    'favicon-default-32.png'        => 32,
    'apple-touch-icon-default.png'  => 180,
    'icon-default-192.png'          => 192,
    'icon-default-512.png'          => 512,
];
foreach ($pngTargets as $fn => $sz) {
    $im = drawLogoBox($sz);
    imagepng($im, PUBLIC_DIR . '/' . $fn, 9);
    imagedestroy($im);
    echo "wrote $fn ({$sz}x{$sz})\n";
}

// ---------- 4. favicon-default.ico ----------
$pngPath = PUBLIC_DIR . '/favicon-default-32.png';
$png = file_get_contents($pngPath);
$pngSize = strlen($png);
$icondir = pack('vvv', 0, 1, 1);
$entry = pack('CCCCvvVV', 32, 32, 0, 0, 1, 32, $pngSize, 22);
file_put_contents(PUBLIC_DIR . '/favicon-default.ico', $icondir . $entry . $png);
echo "wrote favicon-default.ico\n";

// ---------- 5. og-image-default.png 1200x630 ----------
$W = 1200; $H = 630;
$og = imagecreatetruecolor($W, $H);
imagealphablending($og, true);
imagesavealpha($og, true);

// Background gradient horizontal: bg -> bg2 -> bg
for ($x = 0; $x < $W; $x++) {
    $t = $x / $W;
    if ($t < 0.5) {
        $u = $t * 2;
        $r = (int) (BRAND_BG[0] + (BRAND_BG2[0] - BRAND_BG[0]) * $u);
        $g = (int) (BRAND_BG[1] + (BRAND_BG2[1] - BRAND_BG[1]) * $u);
        $b = (int) (BRAND_BG[2] + (BRAND_BG2[2] - BRAND_BG[2]) * $u);
    } else {
        $u = ($t - 0.5) * 2;
        $r = (int) (BRAND_BG2[0] + (BRAND_BG[0] - BRAND_BG2[0]) * $u);
        $g = (int) (BRAND_BG2[1] + (BRAND_BG[1] - BRAND_BG2[1]) * $u);
        $b = (int) (BRAND_BG2[2] + (BRAND_BG[2] - BRAND_BG2[2]) * $u);
    }
    $col = imagecolorallocate($og, $r, $g, $b);
    imageline($og, $x, 0, $x, $H, $col);
}

// Glow accents (alpha-blended ellipses, simulate blur)
$glowI = imagecolorallocatealpha($og, BRAND_INDIGO[0], BRAND_INDIGO[1], BRAND_INDIGO[2], 110);
$glowP = imagecolorallocatealpha($og, BRAND_PURPLE[0], BRAND_PURPLE[1], BRAND_PURPLE[2], 110);
imagefilledellipse($og, 200, 100, 400, 400, $glowI);
imagefilledellipse($og, 1000, 500, 500, 500, $glowP);

// Brand bar accent (left)
$accent = imagecolorallocate($og, BRAND_INDIGO[0], BRAND_INDIGO[1], BRAND_INDIGO[2]);
imagefilledrectangle($og, 60, 260, 66, 370, $accent);

// Text colors
$white   = imagecolorallocate($og, 255, 255, 255);
$indigo3 = imagecolorallocate($og, 129, 140, 248);   // indigo-400
$slate2  = imagecolorallocate($og, 148, 163, 184);   // slate-400
$slate3  = imagecolorallocate($og, 100, 116, 139);   // slate-500
$slate4  = imagecolorallocate($og,  71,  85, 105);   // slate-600
$panelBg = imagecolorallocate($og,  15,  23,  42);   // slate-900
$panelBd = imagecolorallocate($og,  30,  41,  59);   // slate-800

// Heading "LDC Store"
imagettftext($og, 64, 0, 90, 320, $white,   FONT, 'LDC ');
$bbox = imagettfbbox(64, 0, FONT, 'LDC ');
$ldcW = $bbox[2] - $bbox[0];
imagettftext($og, 64, 0, 90 + $ldcW, 320, $indigo3, FONT, 'Store');

// Subtitle
imagettftext($og, 24, 0, 90, 365, $slate2, FONT_REGULAR, 'Blox Fruit Joki & Akun Murah');

// Description (2 lines)
imagettftext($og, 18, 0, 90, 425, $slate3, FONT_REGULAR, 'Jasa joki terpercaya, permanent fruit & gamepass');
imagettftext($og, 18, 0, 90, 455, $slate3, FONT_REGULAR, 'dengan harga terjangkau. Proses cepat & aman.');

// CTA pill
imagefilledrectangle($og, 90, 490, 250, 534, $accent);
imagettftext($og, 16, 0, 115, 519, $white, FONT, 'Hubungi Kami');

// Stats panel
imagefilledrectangle($og, 700, 180, 1120, 460, $panelBg);
imagerectangle($og, 700, 180, 1120, 460, $panelBd);
imagettftext($og, 12, 0, 740, 220, $indigo3, FONT, 'STATS');
imageline($og, 740, 245, 1080, 245, $panelBd);

// Stats rows
$rows = [
    ['193+', 'Joki Selesai',  280],
    ['18+',  'Akun Terjual',  330],
    ['395+', 'Item Terjual',  380],
    ['39',   'Layanan',       430],
];
foreach ($rows as [$num, $label, $y]) {
    imagettftext($og, 32, 0, 740, $y, $white,  FONT, $num);
    imagettftext($og, 16, 0, 880, $y, $slate3, FONT_REGULAR, $label);
}

// URL footer
imagettftext($og, 12, 0, 90, 580, $slate4, FONT_REGULAR, 'ldctesting.my.id');

imagepng($og, PUBLIC_DIR . '/og-image-default.png', 6);
imagedestroy($og);
echo "wrote og-image-default.png (1200x630)\n";

echo "\nAll default branding assets generated.\n";
