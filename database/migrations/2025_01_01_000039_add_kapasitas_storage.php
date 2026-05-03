<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('storage_accounts', function (Blueprint $table) {
            $table->integer('kapasitas_storage')->default(1)->after('aktif');
        });
    }

    public function down(): void
    {
        Schema::table('storage_accounts', function (Blueprint $table) {
            $table->dropColumn('kapasitas_storage');
        });
    }
};
