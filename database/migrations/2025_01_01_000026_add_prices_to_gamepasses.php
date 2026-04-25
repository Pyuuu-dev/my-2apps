<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('gamepasses', function (Blueprint $table) {
            $table->renameColumn('harga', 'harga_robux');
        });

        Schema::table('gamepasses', function (Blueprint $table) {
            $table->bigInteger('harga_beli')->default(0)->after('harga_robux');
            $table->bigInteger('harga_jual')->default(0)->after('harga_beli');
        });
    }

    public function down(): void
    {
        Schema::table('gamepasses', function (Blueprint $table) {
            $table->dropColumn(['harga_beli', 'harga_jual']);
        });

        Schema::table('gamepasses', function (Blueprint $table) {
            $table->renameColumn('harga_robux', 'harga');
        });
    }
};
