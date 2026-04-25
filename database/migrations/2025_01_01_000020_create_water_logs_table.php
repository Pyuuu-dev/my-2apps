<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('water_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_plan_id')->constrained('diet_plans')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('jumlah_ml')->default(250); // default 1 gelas = 250ml
            $table->string('waktu')->nullable(); // pagi, siang, sore, malam
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_logs');
    }
};
