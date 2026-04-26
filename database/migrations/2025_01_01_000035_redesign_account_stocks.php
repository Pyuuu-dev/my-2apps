<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('account_stocks', function (Blueprint $table) {
            $table->string('username_roblox')->nullable()->after('judul');
            $table->string('password_roblox')->nullable()->after('username_roblox');
            $table->string('sword_gun')->nullable()->after('password_roblox');
            $table->string('fruit')->nullable()->after('sword_gun');
            $table->string('belly')->nullable()->after('fruit');
            $table->string('fragment')->nullable()->after('belly');
            $table->string('race')->nullable()->after('fragment');
            $table->bigInteger('harga_jual')->default(0)->after('harga');
        });

        // Rename 'harga' to 'harga_beli' for clarity
        Schema::table('account_stocks', function (Blueprint $table) {
            $table->renameColumn('harga', 'harga_beli');
        });

        // Rename 'judul' to keep but also use as display name
        // judul will now store the display name / label
    }

    public function down(): void
    {
        Schema::table('account_stocks', function (Blueprint $table) {
            $table->renameColumn('harga_beli', 'harga');
        });

        Schema::table('account_stocks', function (Blueprint $table) {
            $table->dropColumn([
                'username_roblox', 'password_roblox', 'sword_gun',
                'fruit', 'belly', 'fragment', 'race', 'harga_jual',
            ]);
        });
    }
};
