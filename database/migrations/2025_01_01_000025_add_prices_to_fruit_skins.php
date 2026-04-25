<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fruit_skins', function (Blueprint $table) {
            $table->renameColumn('harga', 'harga_jual');
        });

        Schema::table('fruit_skins', function (Blueprint $table) {
            $table->bigInteger('harga_beli')->default(0)->after('nama_skin');
        });
    }

    public function down(): void
    {
        Schema::table('fruit_skins', function (Blueprint $table) {
            $table->dropColumn('harga_beli');
        });

        Schema::table('fruit_skins', function (Blueprint $table) {
            $table->renameColumn('harga_jual', 'harga');
        });
    }
};
