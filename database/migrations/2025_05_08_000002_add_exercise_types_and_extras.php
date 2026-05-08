<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom exercise untuk berbagai tipe input
        Schema::table('diet_exercise_logs', function (Blueprint $table) {
            $table->integer('langkah')->nullable()->after('kalori_terbakar'); // steps
            $table->float('jarak_km')->nullable()->after('langkah'); // distance
            $table->string('reps_sets')->nullable()->after('jarak_km'); // "3x12" format
            $table->string('tipe_input')->default('durasi')->after('reps_sets'); // durasi/langkah/jarak/reps
        });

        // Body measurements
        Schema::create('diet_body_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('diet_user_profiles')->cascadeOnDelete();
            $table->date('tanggal');
            $table->float('lingkar_pinggang')->nullable(); // cm
            $table->float('lingkar_dada')->nullable();
            $table->float('lingkar_lengan')->nullable();
            $table->float('lingkar_paha')->nullable();
            $table->float('lingkar_pinggul')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['profile_id', 'tanggal']);
        });

        // Mood & energy tracker
        Schema::table('diet_food_logs', function (Blueprint $table) {
            $table->integer('mood_after')->nullable()->after('catatan'); // 1-5
            $table->integer('energy_after')->nullable()->after('mood_after'); // 1-5
        });

        // Cheat day & calorie banking
        Schema::table('diet_user_profiles', function (Blueprint $table) {
            $table->boolean('is_cheat_day')->default(false)->after('fasting_active');
            $table->integer('calorie_bank')->default(0)->after('is_cheat_day'); // accumulated saved calories
        });

        // Meal prep plans
        Schema::create('diet_meal_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('diet_user_profiles')->cascadeOnDelete();
            $table->date('tanggal'); // untuk hari apa
            $table->json('sarapan')->nullable();
            $table->json('makan_siang')->nullable();
            $table->json('makan_malam')->nullable();
            $table->json('snack')->nullable();
            $table->integer('total_kalori')->default(0);
            $table->integer('total_protein')->default(0);
            $table->text('catatan_ai')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->index(['profile_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_meal_plans');
        Schema::dropIfExists('diet_body_measurements');

        Schema::table('diet_exercise_logs', function (Blueprint $table) {
            $table->dropColumn(['langkah', 'jarak_km', 'reps_sets', 'tipe_input']);
        });

        Schema::table('diet_food_logs', function (Blueprint $table) {
            $table->dropColumn(['mood_after', 'energy_after']);
        });

        Schema::table('diet_user_profiles', function (Blueprint $table) {
            $table->dropColumn(['is_cheat_day', 'calorie_bank']);
        });
    }
};
