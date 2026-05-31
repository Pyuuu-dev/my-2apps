<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Seed default `theme.login_template` setting agar fitur multi-template
     * login langsung tersedia di environment yang sudah pernah seed
     * ThemeSettingsSeeder sebelumnya. Idempotent: pakai upsert by key.
     */
    public function up(): void
    {
        $exists = DB::table('settings')->where('key', 'theme.login_template')->exists();

        if (!$exists) {
            DB::table('settings')->insert([
                'key' => 'theme.login_template',
                'value' => 'modern',
                'group' => 'theme_login',
                'label' => 'Template Halaman Login',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Cache::forget('settings.all');
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'theme.login_template')->delete();
        Cache::forget('settings.all');
    }
};
