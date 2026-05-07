<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // === FOOD FAVORITES (Quick Add) ===
        Schema::create('diet_food_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('diet_user_profiles')->cascadeOnDelete();
            $table->string('nama_makanan');
            $table->string('waktu_makan')->default('snack');
            $table->integer('kalori')->default(0);
            $table->float('protein')->default(0);
            $table->float('karbohidrat')->default(0);
            $table->float('lemak')->default(0);
            $table->float('porsi')->default(1);
            $table->string('satuan_porsi')->default('porsi');
            $table->integer('use_count')->default(0);
            $table->timestamps();

            $table->index(['profile_id', 'use_count']);
        });

        // === INTERMITTENT FASTING ===
        Schema::create('diet_fasting_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('diet_user_profiles')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('tipe', ['16_8', '18_6', '20_4', 'omad', 'custom'])->default('16_8');
            $table->timestamp('mulai_puasa')->nullable();
            $table->timestamp('buka_puasa')->nullable();
            $table->integer('durasi_menit')->nullable();
            $table->boolean('completed')->default(false);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['profile_id', 'tanggal']);
        });

        // === SLEEP TRACKER ===
        Schema::create('diet_sleep_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('diet_user_profiles')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_tidur')->nullable();
            $table->time('jam_bangun')->nullable();
            $table->float('durasi_jam')->nullable();
            $table->integer('kualitas')->nullable(); // 1-5
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['profile_id', 'tanggal']);
        });

        // === Add fields to user profile ===
        Schema::table('diet_user_profiles', function (Blueprint $table) {
            $table->integer('air_target_ml')->nullable()->after('lemak_target');
            $table->integer('ai_requests_today')->default(0)->after('state_data');
            $table->date('ai_requests_date')->nullable()->after('ai_requests_today');
            $table->integer('max_ai_requests')->default(50)->after('ai_requests_date');
            $table->boolean('proactive_nudge')->default(true)->after('max_ai_requests');
            $table->string('fasting_type')->nullable()->after('proactive_nudge');
            $table->boolean('fasting_active')->default(false)->after('fasting_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_sleep_logs');
        Schema::dropIfExists('diet_fasting_logs');
        Schema::dropIfExists('diet_food_favorites');

        Schema::table('diet_user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'air_target_ml', 'ai_requests_today', 'ai_requests_date',
                'max_ai_requests', 'proactive_nudge', 'fasting_type', 'fasting_active',
            ]);
        });
    }
};
