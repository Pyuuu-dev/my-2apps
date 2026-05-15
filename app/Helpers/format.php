<?php

/**
 * Format helpers untuk konsistensi tampilan tanggal & angka.
 *
 * Auto-loaded via composer.json "files".
 */

use Carbon\Carbon;
use Carbon\CarbonInterface;

if (!function_exists('format_rupiah')) {
    /**
     * Format angka jadi "Rp X.XXX.XXX" (tanpa desimal).
     * Aman untuk null/string.
     */
    function format_rupiah($amount, bool $withPrefix = true): string
    {
        $value = is_numeric($amount) ? (float) $amount : 0;
        $formatted = number_format($value, 0, ',', '.');
        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('format_angka')) {
    /**
     * Format angka tanpa "Rp" — pakai locale id_ID (titik ribuan).
     */
    function format_angka($value, int $decimals = 0): string
    {
        $num = is_numeric($value) ? (float) $value : 0;
        return number_format($num, $decimals, ',', '.');
    }
}

if (!function_exists('format_tanggal')) {
    /**
     * Format tanggal Indonesia: "14 Mei 2026"
     * Input: Carbon|string|null
     */
    function format_tanggal($date, string $format = 'd F Y'): string
    {
        if (!$date) return '-';
        $carbon = $date instanceof CarbonInterface ? $date : Carbon::parse($date);
        return $carbon->translatedFormat($format);
    }
}

if (!function_exists('format_tanggal_jam')) {
    /**
     * "14 Mei 2026 15:30"
     */
    function format_tanggal_jam($date): string
    {
        return format_tanggal($date, 'd F Y H:i');
    }
}

if (!function_exists('format_tanggal_singkat')) {
    /**
     * "14/05/2026"
     */
    function format_tanggal_singkat($date): string
    {
        return format_tanggal($date, 'd/m/Y');
    }
}

if (!function_exists('format_bulan')) {
    /**
     * "Mei 2026" dari string YYYY-MM atau Carbon.
     */
    function format_bulan($input): string
    {
        if (!$input) return '-';
        if (is_string($input) && preg_match('/^\d{4}-\d{2}$/', $input)) {
            return Carbon::parse($input . '-01')->translatedFormat('F Y');
        }
        $carbon = $input instanceof CarbonInterface ? $input : Carbon::parse($input);
        return $carbon->translatedFormat('F Y');
    }
}

if (!function_exists('format_relatif')) {
    /**
     * "5 menit yang lalu", "2 jam lalu" dll (locale ID).
     */
    function format_relatif($date): string
    {
        if (!$date) return '-';
        $carbon = $date instanceof CarbonInterface ? $date : Carbon::parse($date);
        return $carbon->locale('id')->diffForHumans();
    }
}

if (!function_exists('salam_waktu')) {
    /**
     * "Pagi" / "Siang" / "Sore" / "Malam" berdasarkan jam sekarang.
     */
    function salam_waktu(?int $hour = null): string
    {
        $h = $hour ?? (int) now()->format('H');
        return $h < 12 ? 'Pagi' : ($h < 15 ? 'Siang' : ($h < 18 ? 'Sore' : 'Malam'));
    }
}

if (!function_exists('format_singkat_angka')) {
    /**
     * 1500 → "1.5k", 2_500_000 → "2.5jt"
     */
    function format_singkat_angka($value): string
    {
        $num = is_numeric($value) ? (float) $value : 0;
        if ($num >= 1_000_000_000) return number_format($num / 1_000_000_000, 1, ',', '.') . 'M';
        if ($num >= 1_000_000) return number_format($num / 1_000_000, 1, ',', '.') . 'jt';
        if ($num >= 1_000) return number_format($num / 1_000, 1, ',', '.') . 'k';
        return (string) (int) $num;
    }
}
