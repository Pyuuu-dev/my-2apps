<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('daily_activities', function (Blueprint $table) {
            $table->integer('langkah_kaki')->nullable()->default(0)->change();
            $table->decimal('jarak_km', 8, 2)->nullable()->default(0)->change();
            $table->integer('kalori_terbakar')->nullable()->default(0)->change();
            $table->decimal('berat_badan', 5, 1)->nullable()->change();
            $table->integer('jam_tidur')->nullable()->default(0)->change();
            $table->integer('air_minum_ml')->nullable()->default(0)->change();
        });
    }

    public function down(): void {}
};
