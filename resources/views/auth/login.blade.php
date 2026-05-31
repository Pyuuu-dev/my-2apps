{{--
    Login page entry. Memilih template berdasarkan setting `theme.login_template`.
    Whitelist diambil dari ThemeSettingsController::ENUMS supaya single source
    of truth dengan controller validation.

    Preview mode: kalau request punya ?_preview=<key>, dan user authenticated
    sebagai admin (dijaga di AuthController::showLogin), pakai template tersebut.
--}}
@php
    $allowed = \App\Http\Controllers\ThemeSettingsController::ENUMS['theme.login_template'];
    $preview = request('_preview');

    if ($preview && auth()->check() && in_array($preview, $allowed, true)) {
        $tpl = $preview;
    } else {
        $tpl = setting('theme.login_template', 'modern');
        $tpl = in_array($tpl, $allowed, true) ? $tpl : 'modern';
    }
@endphp
@include("auth.templates.{$tpl}")
