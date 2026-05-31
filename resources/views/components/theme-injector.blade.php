@php
    /**
     * Inject CSS variables overrides dari setting('theme.*').
     * Hanya value yang non-kosong yang dioverride; sisanya pakai default app.css.
     *
     * Sanitize: semua value hex sudah divalidasi di ThemeSettingsController saat save.
     * Tetap pakai whitelist regex sebagai defense-in-depth di sini.
     */
    $tokens = [
        'accent', 'bg', 'surface', 'text',
        'success', 'warning', 'danger', 'info',
    ];

    $hexRe = '/^#[0-9a-fA-F]{6}$/';

    $light = [];
    $dark  = [];
    foreach ($tokens as $t) {
        $lv = (string) setting("theme.light.$t", '');
        $dv = (string) setting("theme.dark.$t", '');
        if ($lv !== '' && preg_match($hexRe, $lv)) $light[$t] = $lv;
        if ($dv !== '' && preg_match($hexRe, $dv)) $dark[$t]  = $dv;
    }

    // Helper: hex -> rgb komma untuk soft variants
    $hexToRgb = function (string $hex): string {
        $h = ltrim($hex, '#');
        if (strlen($h) !== 6) return '0,0,0';
        return hexdec(substr($h, 0, 2)) . ',' . hexdec(substr($h, 2, 2)) . ',' . hexdec(substr($h, 4, 2));
    };

    // Helper: shift lightness via simple HSL approximation
    // amount > 0 = lighten, amount < 0 = darken (range -1..1)
    $shade = function (string $hex, float $amount): string {
        $h = ltrim($hex, '#');
        if (strlen($h) !== 6) return $hex;
        $r = hexdec(substr($h, 0, 2)); $g = hexdec(substr($h, 2, 2)); $b = hexdec(substr($h, 4, 2));
        $adjust = function ($c) use ($amount) {
            return $amount >= 0
                ? (int) round($c + (255 - $c) * $amount)
                : (int) round($c * (1 + $amount));
        };
        $r = max(0, min(255, $adjust($r)));
        $g = max(0, min(255, $adjust($g)));
        $b = max(0, min(255, $adjust($b)));
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    };

    // Radius scale
    $radius = setting('theme.layout.radius', 'md');
    $radiusMap = [
        'sm' => ['sm' => '4px', 'md' => '6px',  'lg' => '8px',  'xl' => '12px'],
        'md' => ['sm' => '6px', 'md' => '8px',  'lg' => '12px', 'xl' => '16px'],
        'lg' => ['sm' => '8px', 'md' => '12px', 'lg' => '16px', 'xl' => '20px'],
    ];
    $r = $radiusMap[$radius] ?? $radiusMap['md'];

    $reduceMotion = setting('theme.layout.reduce_motion', '0') === '1';
@endphp

<style id="theme-injector">
:root {
@foreach($light as $k => $v)
    --{{ $k }}: {{ $v }};
@if($k === 'accent')
    --accent-hover: {{ $shade($v, -0.08) }};
    --accent-soft: rgba({{ $hexToRgb($v) }}, 0.10);
@endif
@if($k === 'success')
    --success-soft: rgba({{ $hexToRgb($v) }}, 0.10);
@endif
@if($k === 'warning')
    --warning-soft: rgba({{ $hexToRgb($v) }}, 0.10);
@endif
@if($k === 'danger')
    --danger-soft: rgba({{ $hexToRgb($v) }}, 0.10);
@endif
@if($k === 'info')
    --info-soft: rgba({{ $hexToRgb($v) }}, 0.10);
@endif
@endforeach
    --r-sm: {{ $r['sm'] }};
    --r-md: {{ $r['md'] }};
    --r-lg: {{ $r['lg'] }};
    --r-xl: {{ $r['xl'] }};
}

.dark {
@foreach($dark as $k => $v)
    --{{ $k }}: {{ $v }};
@if($k === 'accent')
    --accent-hover: {{ $shade($v, 0.10) }};
    --accent-soft: rgba({{ $hexToRgb($v) }}, 0.16);
@endif
@if($k === 'success')
    --success-soft: rgba({{ $hexToRgb($v) }}, 0.16);
@endif
@if($k === 'warning')
    --warning-soft: rgba({{ $hexToRgb($v) }}, 0.16);
@endif
@if($k === 'danger')
    --danger-soft: rgba({{ $hexToRgb($v) }}, 0.16);
@endif
@if($k === 'info')
    --info-soft: rgba({{ $hexToRgb($v) }}, 0.16);
@endif
@endforeach
}

@if($reduceMotion)
/* Reduce motion preference */
*, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
}
.dot-pulse::after { animation: none !important; }
.reveal { animation: none !important; }
@endif
</style>
