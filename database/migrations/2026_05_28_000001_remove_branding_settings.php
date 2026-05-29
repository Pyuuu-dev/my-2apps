<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Hapus row settings group "branding" karena fitur Logo & Branding
     * sudah dihapus dari aplikasi. Data tidak bisa di-rollback.
     */
    public function up(): void
    {
        DB::table('settings')
            ->whereIn('key', [
                'store.logo_url',
                'store.logo_svg',
                'store.brand_color',
            ])
            ->delete();

        Cache::forget('settings.all');
    }

    public function down(): void
    {
        // Tidak ada rollback — fitur sudah dihapus dari kode.
        // Run SettingsSeeder lama jika perlu re-seed.
    }
};
