<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class ThemeSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // === Mode & Preset ===
            ['key' => 'theme.mode_default', 'value' => 'light',  'group' => 'theme', 'label' => 'Mode Default (light/dark/system)', 'type' => 'text'],
            ['key' => 'theme.preset',       'value' => 'indigo', 'group' => 'theme', 'label' => 'Preset Tema',                      'type' => 'text'],

            // === Warna Light ===
            ['key' => 'theme.light.accent',  'value' => '', 'group' => 'theme_light', 'label' => 'Accent (Light)',  'type' => 'color'],
            ['key' => 'theme.light.bg',      'value' => '', 'group' => 'theme_light', 'label' => 'Background',      'type' => 'color'],
            ['key' => 'theme.light.surface', 'value' => '', 'group' => 'theme_light', 'label' => 'Surface (Card)',  'type' => 'color'],
            ['key' => 'theme.light.text',    'value' => '', 'group' => 'theme_light', 'label' => 'Text',            'type' => 'color'],
            ['key' => 'theme.light.success', 'value' => '', 'group' => 'theme_light', 'label' => 'Success',         'type' => 'color'],
            ['key' => 'theme.light.warning', 'value' => '', 'group' => 'theme_light', 'label' => 'Warning',         'type' => 'color'],
            ['key' => 'theme.light.danger',  'value' => '', 'group' => 'theme_light', 'label' => 'Danger',          'type' => 'color'],
            ['key' => 'theme.light.info',    'value' => '', 'group' => 'theme_light', 'label' => 'Info',            'type' => 'color'],

            // === Warna Dark ===
            ['key' => 'theme.dark.accent',  'value' => '', 'group' => 'theme_dark', 'label' => 'Accent (Dark)',   'type' => 'color'],
            ['key' => 'theme.dark.bg',      'value' => '', 'group' => 'theme_dark', 'label' => 'Background',      'type' => 'color'],
            ['key' => 'theme.dark.surface', 'value' => '', 'group' => 'theme_dark', 'label' => 'Surface (Card)',  'type' => 'color'],
            ['key' => 'theme.dark.text',    'value' => '', 'group' => 'theme_dark', 'label' => 'Text',            'type' => 'color'],
            ['key' => 'theme.dark.success', 'value' => '', 'group' => 'theme_dark', 'label' => 'Success',         'type' => 'color'],
            ['key' => 'theme.dark.warning', 'value' => '', 'group' => 'theme_dark', 'label' => 'Warning',         'type' => 'color'],
            ['key' => 'theme.dark.danger',  'value' => '', 'group' => 'theme_dark', 'label' => 'Danger',          'type' => 'color'],
            ['key' => 'theme.dark.info',    'value' => '', 'group' => 'theme_dark', 'label' => 'Info',            'type' => 'color'],

            // === Layout ===
            ['key' => 'theme.layout.radius',           'value' => 'md',     'group' => 'theme_layout', 'label' => 'Border Radius (sm/md/lg)',          'type' => 'text'],
            ['key' => 'theme.layout.density',          'value' => 'normal', 'group' => 'theme_layout', 'label' => 'Density (compact/normal/comfortable)', 'type' => 'text'],
            ['key' => 'theme.layout.font_family',      'value' => 'inter',  'group' => 'theme_layout', 'label' => 'Font Family',                       'type' => 'text'],
            ['key' => 'theme.layout.sidebar_variant',  'value' => 'subtle', 'group' => 'theme_layout', 'label' => 'Sidebar Variant',                   'type' => 'text'],
            ['key' => 'theme.layout.reduce_motion',    'value' => '0',      'group' => 'theme_layout', 'label' => 'Reduce Motion (0/1)',               'type' => 'text'],

            // === User Presets (custom presets disimpan user) — JSON array ===
            ['key' => 'theme.user_presets',            'value' => '[]',     'group' => 'theme_presets', 'label' => 'User Presets (JSON)',              'type' => 'textarea'],
        ];

        foreach ($defaults as $row) {
            // Hanya buat kalau belum ada — JANGAN overwrite kalau user sudah custom.
            Setting::firstOrCreate(['key' => $row['key']], $row);
        }
    }
}
