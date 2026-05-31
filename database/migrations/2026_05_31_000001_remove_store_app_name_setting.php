<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Hapus row setting `store.app_name`. Aplikasi kini menggunakan
     * `store.brand_name` sebagai single source of truth untuk nama brand
     * (digunakan di layout, login, manifest, dan landing page).
     */
    public function up(): void
    {
        DB::table('settings')
            ->where('key', 'store.app_name')
            ->delete();

        Cache::forget('settings.all');
    }

    public function down(): void
    {
        // Tidak ada rollback — key ini sudah tidak digunakan di kode.
        // Jalankan SettingsSeeder lama bila perlu re-seed.
    }
};
