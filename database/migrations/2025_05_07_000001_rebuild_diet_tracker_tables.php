<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop semua tabel diet lama
        $tables = [
            'fasting_logs', 'water_logs', 'monthly_logs', 'reminders',
            'daily_activities', 'exercises', 'meals', 'weight_logs',
            'exercise_database', 'food_database', 'diet_plans',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        // === USER PROFILE ===
        Schema::create('diet_user_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('telegram_chat_id')->unique();
            $table->string('nama')->nullable();
            $table->string('username')->nullable();
            $table->enum('gender', ['pria', 'wanita'])->nullable();
            $table->integer('umur')->nullable();
            $table->float('tinggi_cm')->nullable();
            $table->float('berat_kg')->nullable();
            $table->float('berat_target')->nullable();
            $table->enum('level_aktivitas', ['sedentary', 'light', 'moderate', 'active', 'very_active'])->default('moderate');
            $table->enum('goal', ['cutting', 'bulking', 'maintenance', 'diet'])->default('diet');
            $table->integer('kalori_target')->nullable();
            $table->integer('protein_target')->nullable(); // gram
            $table->integer('karbo_target')->nullable(); // gram
            $table->integer('lemak_target')->nullable(); // gram
            $table->float('bmr')->nullable();
            $table->float('tdee')->nullable();
            $table->float('bmi')->nullable();
            $table->float('body_fat_pct')->nullable();
            $table->string('timezone')->default('Asia/Singapore');
            $table->boolean('aktif')->default(true);
            $table->string('state')->nullable(); // untuk conversation state
            $table->json('state_data')->nullable(); // data sementara conversation
            $table->timestamps();
        });

        // === FOOD LOG (Catatan Makanan) ===
        Schema::create('diet_food_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('diet_user_profiles')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('waktu_makan', ['sarapan', 'makan_siang', 'makan_malam', 'snack']);
            $table->string('nama_makanan');
            $table->float('porsi')->default(1);
            $table->string('satuan_porsi')->default('porsi');
            $table->integer('kalori')->default(0);
            $table->float('protein')->default(0);
            $table->float('karbohidrat')->default(0);
            $table->float('lemak')->default(0);
            $table->string('foto_url')->nullable();
            $table->enum('sumber', ['manual', 'foto', 'database'])->default('manual');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['profile_id', 'tanggal']);
        });

        // === WEIGHT LOG (Catatan Berat Badan) ===
        Schema::create('diet_weight_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('diet_user_profiles')->cascadeOnDelete();
            $table->date('tanggal');
            $table->float('berat_kg');
            $table->float('bmi')->nullable();
            $table->float('body_fat_pct')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['profile_id', 'tanggal']);
        });

        // === WATER LOG (Catatan Minum Air) ===
        Schema::create('diet_water_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('diet_user_profiles')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('jumlah_ml');
            $table->time('waktu');
            $table->timestamps();

            $table->index(['profile_id', 'tanggal']);
        });

        // === EXERCISE LOG (Catatan Olahraga) ===
        Schema::create('diet_exercise_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('diet_user_profiles')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('jenis_olahraga');
            $table->integer('durasi_menit');
            $table->integer('kalori_terbakar')->default(0);
            $table->enum('intensitas', ['ringan', 'sedang', 'berat'])->default('sedang');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['profile_id', 'tanggal']);
        });

        // === REMINDERS ===
        Schema::create('diet_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('diet_user_profiles')->cascadeOnDelete();
            $table->enum('tipe', ['minum', 'makan', 'olahraga', 'tidur', 'custom']);
            $table->string('judul');
            $table->text('pesan');
            $table->time('waktu');
            $table->json('hari_aktif')->nullable(); // [1,2,3,4,5,6,7] senin-minggu
            $table->boolean('aktif')->default(true);
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();
        });

        // === GAMIFICATION: STREAKS ===
        Schema::create('diet_streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('diet_user_profiles')->cascadeOnDelete();
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_log_date')->nullable();
            $table->integer('total_days_logged')->default(0);
            $table->timestamps();
        });

        // === GAMIFICATION: BADGES ===
        Schema::create('diet_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('diet_user_profiles')->cascadeOnDelete();
            $table->string('badge_code'); // e.g. 'streak_7', 'first_log', 'weight_goal'
            $table->string('badge_name');
            $table->string('badge_icon')->default('🏆');
            $table->text('deskripsi')->nullable();
            $table->timestamp('earned_at');
            $table->timestamps();

            $table->unique(['profile_id', 'badge_code']);
        });

        // === FOOD DATABASE (Referensi Makanan Indonesia) ===
        Schema::create('diet_food_database', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori')->nullable(); // nasi, lauk, sayur, minuman, snack, dll
            $table->integer('kalori'); // per porsi
            $table->float('protein')->default(0);
            $table->float('karbohidrat')->default(0);
            $table->float('lemak')->default(0);
            $table->string('satuan_porsi')->default('1 porsi');
            $table->integer('berat_gram')->nullable();
            $table->timestamps();

            $table->index('nama');
            $table->index('kategori');
        });

        // === AI REQUEST LOG ===
        Schema::create('diet_ai_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->nullable()->constrained('diet_user_profiles')->nullOnDelete();
            $table->enum('tipe', ['text', 'vision', 'recommendation']);
            $table->string('model_used');
            $table->text('prompt')->nullable();
            $table->text('response')->nullable();
            $table->integer('tokens_used')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // === DAILY SUMMARY CACHE ===
        Schema::create('diet_daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('diet_user_profiles')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('total_kalori')->default(0);
            $table->float('total_protein')->default(0);
            $table->float('total_karbo')->default(0);
            $table->float('total_lemak')->default(0);
            $table->integer('total_air_ml')->default(0);
            $table->integer('total_exercise_menit')->default(0);
            $table->integer('total_kalori_terbakar')->default(0);
            $table->integer('sisa_kalori')->default(0);
            $table->float('pct_target')->default(0); // persentase target tercapai
            $table->timestamps();

            $table->unique(['profile_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_daily_summaries');
        Schema::dropIfExists('diet_ai_logs');
        Schema::dropIfExists('diet_food_database');
        Schema::dropIfExists('diet_badges');
        Schema::dropIfExists('diet_streaks');
        Schema::dropIfExists('diet_reminders');
        Schema::dropIfExists('diet_exercise_logs');
        Schema::dropIfExists('diet_water_logs');
        Schema::dropIfExists('diet_weight_logs');
        Schema::dropIfExists('diet_food_logs');
        Schema::dropIfExists('diet_user_profiles');
    }
};
