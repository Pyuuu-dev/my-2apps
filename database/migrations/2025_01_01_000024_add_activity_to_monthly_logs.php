<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('monthly_logs', function (Blueprint $table) {
            $table->integer('avg_langkah')->default(0)->after('konsistensi_persen');
            $table->decimal('avg_tidur', 3, 1)->default(0)->after('avg_langkah');
            $table->integer('avg_air_minum')->default(0)->after('avg_tidur');
            $table->integer('total_hari_aktivitas')->default(0)->after('avg_air_minum');
        });
    }

    public function down(): void
    {
        Schema::table('monthly_logs', function (Blueprint $table) {
            $table->dropColumn(['avg_langkah', 'avg_tidur', 'avg_air_minum', 'total_hari_aktivitas']);
        });
    }
};
