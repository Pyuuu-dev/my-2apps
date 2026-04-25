<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fasting_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_plan_id')->constrained('diet_plans')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('tipe')->default('sunnah'); // sunnah, ramadhan, senin_kamis, custom
            $table->time('waktu_sahur')->default('04:00');
            $table->time('waktu_berbuka')->default('18:15');
            $table->boolean('completed')->default(false); // apakah puasa full atau batal
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['diet_plan_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasting_logs');
    }
};
