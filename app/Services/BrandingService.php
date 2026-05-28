<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * BrandingService
 *
 * Manages branding assets (favicon, logo, OG image) backed by the
 * `settings` table. Generates favicon variants (32, 180, 192, 512, .ico)
 * from a single uploaded square PNG/JPG using the GD extension.
 *
 * Public storage layout (after `php artisan storage:link`):
 *   storage/app/public/branding/{filename}                   <- uploaded sources
 *   storage/app/public/branding/generated/favicon-32.png     <- auto-resized
 *   storage/app/public/branding/generated/apple-touch-icon.png
 *   storage/app/public/branding/generated/icon-192.png
 *   storage/app/public/branding/generated/icon-512.png
 *   storage/app/public/branding/generated/favicon.ico        <- ICO (PNG payload)
 *
 * Fallbacks (committed, never overwritten):
 *   public/favicon-default.svg
 *   public/favicon-default-32.png
 *   public/apple-touch-icon-default.png
 *   public/icon-default-192.png
 *   public/icon-default-512.png
 *   public/og-image-default.png
 */
class BrandingService
{
    private const GENERATED_DIR = 'branding/generated';

    /**
     * Regenerate all favicon variants from the uploaded source PNG/JPG.
     * Source must already be saved at storage/app/public/{$sourcePath}.
     */
    public function regenerateFavicons(string $sourcePath): bool
    {
        $absolute = storage_path('app/public/' . $sourcePath);
        if (!is_file($absolute)) {
            Log::warning('BrandingService: source missing', ['path' => $absolute]);
            return false;
        }

        $source = $this->loadImage($absolute);
        if ($source === null) {
            Log::warning('BrandingService: failed to load source image');
            return false;
        }

        $outputDir = storage_path('app/public/' . self::GENERATED_DIR);
        $this->ensureDir($outputDir);

        $sizes = [
            'favicon-32.png'        => 32,
            'apple-touch-icon.png'  => 180,
            'icon-192.png'          => 192,
            'icon-512.png'          => 512,
        ];

        foreach ($sizes as $filename => $size) {
            $resized = $this->resizeSquare($source, $size);
            $path = $outputDir . '/' . $filename;
            imagepng($resized, $path, 9);
            imagedestroy($resized);
        }

        // Build favicon.ico containing the 32x32 PNG payload (modern ICO format)
        $this->generateIco(
            $outputDir . '/favicon-32.png',
            $outputDir . '/favicon.ico'
        );

        imagedestroy($source);

        // Make files world-readable so nginx can serve them
        @chmod($outputDir, 0755);
        foreach (glob($outputDir . '/*') as $f) {
            @chmod($f, 0644);
        }

        return true;
    }

    /**
     * Get URL for a favicon variant. Returns customized URL (with mtime
     * cache-buster) when present, or fallback to committed default.
     *
     * @param string $type One of: svg, png32, apple, png192, png512, ico
     */
    public function getFaviconUrl(string $type): string
    {
        // Customized SVG comes from inline setting, not from a file —
        // for SVG path we serve a tiny inline data URI is overkill;
        // instead we generate /favicon.svg endpoint or fall back to default.
        if ($type === 'svg') {
            $logoSvg = setting('store.logo_svg');
            if (!empty($logoSvg)) {
                // Use route that returns the inline SVG with proper headers
                return route('branding.logo.svg');
            }
            return $this->fallbackUrl('favicon-default.svg');
        }

        $map = [
            'png32'  => self::GENERATED_DIR . '/favicon-32.png',
            'apple'  => self::GENERATED_DIR . '/apple-touch-icon.png',
            'png192' => self::GENERATED_DIR . '/icon-192.png',
            'png512' => self::GENERATED_DIR . '/icon-512.png',
            'ico'    => self::GENERATED_DIR . '/favicon.ico',
        ];

        $relative = $map[$type] ?? null;
        if ($relative === null) {
            return $this->fallbackUrl('favicon-default-32.png');
        }

        $absolute = storage_path('app/public/' . $relative);
        if (is_file($absolute)) {
            return asset('storage/' . $relative) . '?v=' . filemtime($absolute);
        }

        // Fallback to default static
        $defaultMap = [
            'png32'  => 'favicon-default-32.png',
            'apple'  => 'apple-touch-icon-default.png',
            'png192' => 'icon-default-192.png',
            'png512' => 'icon-default-512.png',
            'ico'    => 'favicon-default.ico',
        ];
        return $this->fallbackUrl($defaultMap[$type] ?? 'favicon-default-32.png');
    }

    /**
     * Get OG image URL. Customized upload preferred, fallback to default generated.
     */
    public function getOgImageUrl(): string
    {
        $path = setting('store.og_image_path');
        if (!empty($path)) {
            $absolute = storage_path('app/public/' . $path);
            if (is_file($absolute)) {
                return asset('storage/' . $path) . '?v=' . filemtime($absolute);
            }
        }
        return $this->fallbackUrl('og-image-default.png');
    }

    /**
     * Inline SVG string (sanitized) for the navbar/sidebar/login logo.
     * Returns null when no custom logo set; caller should render default <svg>.
     */
    public function getLogoSvg(): ?string
    {
        $svg = setting('store.logo_svg');
        return !empty($svg) ? $svg : null;
    }

    // ---------- helpers ----------

    private function fallbackUrl(string $filename): string
    {
        $absolute = public_path($filename);
        $version = is_file($absolute) ? filemtime($absolute) : time();
        return asset($filename) . '?v=' . $version;
    }

    private function loadImage(string $path)
    {
        $info = @getimagesize($path);
        if ($info === false) return null;

        return match ($info['mime']) {
            'image/png'  => imagecreatefrompng($path),
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : null,
            default      => null,
        };
    }

    private function resizeSquare($source, int $size)
    {
        $sw = imagesx($source);
        $sh = imagesy($source);

        $out = imagecreatetruecolor($size, $size);
        imagealphablending($out, false);
        imagesavealpha($out, true);
        $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
        imagefilledrectangle($out, 0, 0, $size, $size, $transparent);

        // Centered crop to square first if non-square input
        $crop = min($sw, $sh);
        $sx = (int) (($sw - $crop) / 2);
        $sy = (int) (($sh - $crop) / 2);

        imagecopyresampled($out, $source, 0, 0, $sx, $sy, $size, $size, $crop, $crop);

        return $out;
    }

    /**
     * Build a minimal ICO file embedding a single PNG image.
     * Format spec: ICONDIR (6) + ICONDIRENTRY (16) + PNG payload.
     */
    private function generateIco(string $pngPath, string $icoPath): void
    {
        if (!is_file($pngPath)) return;

        $png = file_get_contents($pngPath);
        $pngSize = strlen($png);
        $info = getimagesizefromstring($png);
        $w = $info ? min($info[0], 255) : 32;
        $h = $info ? min($info[1], 255) : 32;
        // 0 means 256 in ICO format, clamp safely
        $widthByte = ($w >= 256) ? 0 : $w;
        $heightByte = ($h >= 256) ? 0 : $h;

        // ICONDIR header
        $icondir = pack('vvv', 0, 1, 1); // reserved=0, type=icon, count=1

        // ICONDIRENTRY (16 bytes)
        $entry = pack(
            'CCCCvvVV',
            $widthByte,    // width
            $heightByte,   // height
            0,             // colorCount (0 = no palette)
            0,             // reserved
            1,             // planes
            32,            // bitCount
            $pngSize,      // bytesInRes
            22             // imageOffset (6 + 16)
        );

        file_put_contents($icoPath, $icondir . $entry . $png);
    }

    private function ensureDir(string $dir): void
    {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    /**
     * Build the manifest JSON for /site.webmanifest dynamic endpoint.
     */
    public function buildManifest(): array
    {
        $brand = setting('store.brand_name', 'LDC Store');
        $themeColor = setting('store.brand_color', '#020617');

        return [
            'name' => $brand . ' - Blox Fruit Joki & Akun Murah',
            'short_name' => $brand,
            'description' => 'Jasa joki Blox Fruit terpercaya, permanent fruit & gamepass murah',
            'icons' => [
                ['src' => $this->getFaviconUrl('png192'), 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => $this->getFaviconUrl('png512'), 'sizes' => '512x512', 'type' => 'image/png'],
            ],
            'theme_color' => $themeColor,
            'background_color' => $themeColor,
            'display' => 'standalone',
            'start_url' => '/',
            'scope' => '/',
        ];
    }
}
