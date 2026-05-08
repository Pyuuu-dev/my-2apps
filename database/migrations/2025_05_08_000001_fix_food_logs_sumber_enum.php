<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite: recreate table with updated CHECK constraint
        DB::statement('PRAGMA foreign_keys=OFF');

        // Create new table with correct enum
        DB::statement('CREATE TABLE "diet_food_logs_new" (
            "id" integer primary key autoincrement not null,
            "profile_id" integer not null,
            "tanggal" date not null,
            "waktu_makan" varchar check ("waktu_makan" in (\'sarapan\', \'makan_siang\', \'makan_malam\', \'snack\')) not null,
            "nama_makanan" varchar not null,
            "porsi" float not null default \'1\',
            "satuan_porsi" varchar not null default \'porsi\',
            "kalori" integer not null default \'0\',
            "protein" float not null default \'0\',
            "karbohidrat" float not null default \'0\',
            "lemak" float not null default \'0\',
            "foto_url" varchar,
            "sumber" varchar check ("sumber" in (\'manual\', \'foto\', \'database\', \'ai\')) not null default \'manual\',
            "catatan" text,
            "created_at" datetime,
            "updated_at" datetime,
            foreign key("profile_id") references "diet_user_profiles"("id") on delete cascade
        )');

        // Copy data
        DB::statement('INSERT INTO "diet_food_logs_new" SELECT * FROM "diet_food_logs"');

        // Drop old, rename new
        DB::statement('DROP TABLE "diet_food_logs"');
        DB::statement('ALTER TABLE "diet_food_logs_new" RENAME TO "diet_food_logs"');

        // Recreate index
        DB::statement('CREATE INDEX "diet_food_logs_profile_id_tanggal_index" ON "diet_food_logs" ("profile_id", "tanggal")');

        DB::statement('PRAGMA foreign_keys=ON');
    }

    public function down(): void
    {
        // Revert not needed
    }
};
