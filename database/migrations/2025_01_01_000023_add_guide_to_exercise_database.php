<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exercise_database', function (Blueprint $table) {
            $table->text('instruksi')->nullable()->after('deskripsi');
            $table->text('manfaat')->nullable()->after('instruksi');
            $table->string('otot_target')->nullable()->after('manfaat');
            $table->integer('durasi_rekomendasi')->default(30)->after('otot_target');
            $table->string('set_rep')->nullable()->after('durasi_rekomendasi');
            $table->string('level')->default('pemula')->after('set_rep'); // pemula, menengah, lanjutan
            $table->string('peralatan')->default('tanpa_alat')->after('level');
        });
    }

    public function down(): void
    {
        Schema::table('exercise_database', function (Blueprint $table) {
            $table->dropColumn(['instruksi', 'manfaat', 'otot_target', 'durasi_rekomendasi', 'set_rep', 'level', 'peralatan']);
        });
    }
};
