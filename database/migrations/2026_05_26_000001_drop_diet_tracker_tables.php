<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Drop seluruh tabel & artefak modul DietTracker.
 *
 * Modul DietTracker dihapus total — project sekarang hanya berisi
 * BloxFruit (LDC Store Management). Migration ini:
 *   1. Drop 16 tabel diet_*
 *   2. Hapus 21 entry migration lama dari tabel migrations supaya
 *      migrate:status tetap bersih setelah file fisiknya dihapus.
 *
 * Irreversible — data diet hilang permanen.
 */
return new class extends Migration {
    public function up(): void
    {
        $tables = [
            'diet_ai_logs',
            'diet_badges',
            'diet_body_measurements',
            'diet_daily_summaries',
            'diet_exercise_logs',
            'diet_fasting_logs',
            'diet_food_database',
            'diet_food_favorites',
            'diet_food_logs',
            'diet_meal_plans',
            'diet_reminders',
            'diet_sleep_logs',
            'diet_streaks',
            'diet_user_profiles',
            'diet_water_logs',
            'diet_weight_logs',
        ];

        // Disable FK checks (SQLite ignores ini, MySQL menghormati)
        Schema::disableForeignKeyConstraints();
        foreach ($tables as $t) {
            Schema::dropIfExists($t);
        }
        Schema::enableForeignKeyConstraints();

        // Bersihkan entry migration diet lama supaya artisan migrate:status bersih
        $orphanMigrations = [
            '2025_01_01_000010_create_diet_plans_table',
            '2025_01_01_000011_create_meals_table',
            '2025_01_01_000012_create_exercises_table',
            '2025_01_01_000013_create_daily_activities_table',
            '2025_01_01_000014_create_reminders_table',
            '2025_01_01_000015_create_weight_logs_table',
            '2025_01_01_000016_create_food_database_table',
            '2025_01_01_000017_create_exercise_database_table',
            '2025_01_01_000018_add_body_profile_to_diet_plans',
            '2025_01_01_000019_create_monthly_logs_table',
            '2025_01_01_000020_create_water_logs_table',
            '2025_01_01_000021_add_last_sent_to_reminders',
            '2025_01_01_000022_create_fasting_logs_table',
            '2025_01_01_000023_add_guide_to_exercise_database',
            '2025_01_01_000024_add_activity_to_monthly_logs',
            '2025_01_01_000037_fix_daily_activities_nullable',
            '2025_05_07_000001_rebuild_diet_tracker_tables',
            '2025_05_07_000002_seed_indonesian_food_database',
            '2025_05_07_000003_add_diet_extra_features',
            '2025_05_08_000001_fix_food_logs_sumber_enum',
            '2025_05_08_000002_add_exercise_types_and_extras',
        ];

        // Drop sisa tabel schema lama (kalau masih ada di environment lain)
        $legacyTables = [
            'monthly_logs', 'water_logs', 'weight_logs', 'reminders',
            'daily_activities', 'exercises', 'meals', 'diet_plans',
            'food_database', 'exercise_database', 'fasting_logs',
        ];
        Schema::disableForeignKeyConstraints();
        foreach ($legacyTables as $t) {
            Schema::dropIfExists($t);
        }
        Schema::enableForeignKeyConstraints();

        DB::table('migrations')->whereIn('migration', $orphanMigrations)->delete();
    }

    public function down(): void
    {
        // Irreversible — modul DietTracker sudah dihapus dari codebase.
    }
};
