<?php

namespace App\Services;

/**
 * BrandingService
 *
 * Provides branding asset URLs for the landing page (favicon variants,
 * OG image, manifest payload). Currently serves static defaults committed
 * to public/, with cache-bust ?v=mtime.
 *
 * Logo customization (URL or inline SVG) is handled inline in views via
 * the <x-brand-logo> component reading directly from the settings.
 */
class BrandingService
{
    /**
     * Get URL for a favicon variant. Always serves the committed default
     * static under public/ with mtime cache-buster.
     *
     * @param string $type One of: svg, png32, apple, png192, png512, ico
     */
    public function getFaviconUrl(string $type): string
    {
        // SVG: serve customized inline logo via dedicated route when set,
        // otherwise fall back to default static SVG.
        if ($type === 'svg') {
            $logoSvg = setting('store.logo_svg');
            if (!empty($logoSvg)) {
                return route('branding.logo.svg');
            }
            return $this->fallbackUrl('favicon-default.svg');
        }

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
     * Get OG image URL. Always uses default static (no upload feature).
     */
    public function getOgImageUrl(): string
    {
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

    /**
     * Build the manifest JSON payload for /site.webmanifest.
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

    private function fallbackUrl(string $filename): string
    {
        $absolute = public_path($filename);
        $version = is_file($absolute) ? filemtime($absolute) : time();
        return asset($filename) . '?v=' . $version;
    }
}
