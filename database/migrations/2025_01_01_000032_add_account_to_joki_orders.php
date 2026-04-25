<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('joki_orders', function (Blueprint $table) {
            $table->string('username_roblox')->nullable()->after('kontak');
            $table->string('password_roblox')->nullable()->after('username_roblox');
        });
    }

    public function down(): void
    {
        Schema::table('joki_orders', function (Blueprint $table) {
            $table->dropColumn(['username_roblox', 'password_roblox']);
        });
    }
};
