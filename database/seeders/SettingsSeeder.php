<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Store / Brand
            ['key' => 'store.brand_name', 'value' => 'LDC Store', 'group' => 'store', 'label' => 'Nama Brand Store', 'type' => 'text'],
            ['key' => 'store.app_name', 'value' => 'MyApp', 'group' => 'store', 'label' => 'Nama Aplikasi Internal', 'type' => 'text'],
            ['key' => 'store.tagline', 'value' => 'Management Tools', 'group' => 'store', 'label' => 'Tagline', 'type' => 'text'],

            // Kontak
            ['key' => 'store.wa_number', 'value' => '6282353085502', 'group' => 'kontak', 'label' => 'Nomor WhatsApp (62...)', 'type' => 'tel'],
            ['key' => 'store.wa_channel_url', 'value' => '', 'group' => 'kontak', 'label' => 'URL Saluran WhatsApp', 'type' => 'url'],
            ['key' => 'store.tiktok_url', 'value' => 'https://www.tiktok.com/@ldc_storee', 'group' => 'kontak', 'label' => 'URL TikTok', 'type' => 'url'],
            ['key' => 'store.instagram_url', 'value' => 'https://www.instagram.com/ldcstoree/', 'group' => 'kontak', 'label' => 'URL Instagram', 'type' => 'url'],
            ['key' => 'store.discord_url', 'value' => '', 'group' => 'kontak', 'label' => 'URL Discord', 'type' => 'url'],
            ['key' => 'store.drive_url', 'value' => '', 'group' => 'kontak', 'label' => 'URL Google Drive', 'type' => 'url'],

            // Copy template
            ['key' => 'store.copy_header_template', 'value' => "📌 LDC STORE — STOK\n\n", 'group' => 'copy', 'label' => 'Header Template Copy Stok', 'type' => 'textarea'],

            // App config
            ['key' => 'app.timezone_label', 'value' => 'SGT', 'group' => 'app', 'label' => 'Label Timezone (SGT/WIB/...)', 'type' => 'text'],
        ];

        foreach ($defaults as $row) {
            Setting::updateOrCreate(['key' => $row['key']], $row);
        }
    }
}
