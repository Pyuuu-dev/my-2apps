<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('profit_records', function (Blueprint $table) {
            $table->unsignedBigInteger('joki_order_id')->nullable()->after('metode_bayar');
        });
    }

    public function down(): void
    {
        Schema::table('profit_records', function (Blueprint $table) {
            $table->dropColumn('joki_order_id');
        });
    }
};
