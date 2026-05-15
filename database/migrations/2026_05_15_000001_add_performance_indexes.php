<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Performance indexes — additive only, reversible.
 *
 * Tujuan: mempercepat query yang sering dipakai di dashboard, rekap,
 * pencarian, dan analisa harga.
 *
 * Tidak menghapus atau mengubah data apapun.
 * Beberapa tabel stocks sudah punya unique composite index (skin/fruit/gamepass),
 * jadi tidak perlu duplikasi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('joki_orders', function (Blueprint $table) {
            $table->index('status', 'idx_joki_status');
            $table->index('tanggal_selesai', 'idx_joki_tgl_selesai');
            $table->index(['status', 'created_at'], 'idx_joki_status_created');
        });

        Schema::table('profit_records', function (Blueprint $table) {
            $table->index('tanggal', 'idx_profit_tanggal');
            $table->index('kategori', 'idx_profit_kategori');
            $table->index(['tanggal', 'kategori'], 'idx_profit_tgl_kategori');
        });

        Schema::table('account_stocks', function (Blueprint $table) {
            $table->index('status', 'idx_account_status');
        });

        // permanent_fruit_stocks belum punya composite index seperti yang lain
        Schema::table('permanent_fruit_stocks', function (Blueprint $table) {
            $table->index(['storage_account_id', 'permanent_fruit_price_id'], 'idx_perm_storage_price');
        });
    }

    public function down(): void
    {
        Schema::table('joki_orders', function (Blueprint $table) {
            $table->dropIndex('idx_joki_status');
            $table->dropIndex('idx_joki_tgl_selesai');
            $table->dropIndex('idx_joki_status_created');
        });

        Schema::table('profit_records', function (Blueprint $table) {
            $table->dropIndex('idx_profit_tanggal');
            $table->dropIndex('idx_profit_kategori');
            $table->dropIndex('idx_profit_tgl_kategori');
        });

        Schema::table('account_stocks', function (Blueprint $table) {
            $table->dropIndex('idx_account_status');
        });

        Schema::table('permanent_fruit_stocks', function (Blueprint $table) {
            $table->dropIndex('idx_perm_storage_price');
        });
    }
};
