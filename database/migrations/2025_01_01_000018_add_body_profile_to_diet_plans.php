<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('diet_plans', function (Blueprint $table) {
            $table->string('gender')->default('pria')->after('nama'); // pria, wanita
            $table->integer('umur')->default(25)->after('gender');
            $table->decimal('tinggi_cm', 5, 1)->default(170)->after('umur');
            $table->string('level_aktivitas')->default('sedang')->after('tinggi_cm'); // tidak_aktif, ringan, sedang, aktif, sangat_aktif
            $table->integer('bmr')->default(0)->after('kalori_harian_target');
            $table->integer('tdee')->default(0)->after('bmr');
        });
    }
    public function down(): void
    {
        Schema::table('diet_plans', function (Blueprint $table) {
            $table->dropColumn(['gender', 'umur', 'tinggi_cm', 'level_aktivitas', 'bmr', 'tdee']);
        });
    }
};
