<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('permanent_fruit_stocks', function (Blueprint $table) {
            $table->unsignedBigInteger('permanent_fruit_price_id')->nullable()->after('blox_fruit_id');
        });
    }

    public function down(): void
    {
        Schema::table('permanent_fruit_stocks', function (Blueprint $table) {
            $table->dropColumn('permanent_fruit_price_id');
        });
    }
};
