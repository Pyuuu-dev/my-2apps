<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

// === BloxFruit Controllers ===
use App\Http\Controllers\BloxFruit\DashboardController as BloxDashboard;
use App\Http\Controllers\BloxFruit\BloxFruitController;
use App\Http\Controllers\BloxFruit\FruitSkinController;
use App\Http\Controllers\BloxFruit\SearchController;
use App\Http\Controllers\BloxFruit\StorageAccountController;
use App\Http\Controllers\BloxFruit\GamepassController;
use App\Http\Controllers\BloxFruit\AccountStockController;
use App\Http\Controllers\BloxFruit\JokiOrderController;
use App\Http\Controllers\BloxFruit\PermanentFruitPriceController;
use App\Http\Controllers\BloxFruit\ProfitController;
use App\Http\Controllers\BloxFruit\JokiServiceController;
use App\Http\Controllers\BloxFruit\QuickSellController;

// === DietTracker Controllers ===
use App\Http\Controllers\DietTracker\DashboardController as DietDashboard;
use App\Http\Controllers\DietTracker\AiLogController;

/*
|--------------------------------------------------------------------------
| Auth Routes (Public)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Public Landing Page (root)
|--------------------------------------------------------------------------
*/
Route::get('/', [\App\Http\Controllers\BloxFruit\LandingController::class, 'index'])->name('landing');

// Backward compat: /store -> / (permanent redirect)
Route::redirect('/store', '/', 301);

/*
|--------------------------------------------------------------------------
| Telegram Webhook (no auth, no CSRF)
|--------------------------------------------------------------------------
*/
Route::post('/webhook/telegram-diet', [\App\Http\Controllers\DietTracker\TelegramWebhookController::class, 'handle'])->name('telegram.webhook');

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

Route::get('/backup/download', [\App\Http\Controllers\BackupController::class, 'download'])->name('backup.download');
Route::post('/backup/telegram', [\App\Http\Controllers\BackupController::class, 'sendToTelegram'])->name('backup.telegram');
Route::post('/backup/config', [\App\Http\Controllers\BackupController::class, 'saveConfig'])->name('backup.config');
Route::post('/backup/test', [\App\Http\Controllers\BackupController::class, 'testBackupBot'])->name('backup.test');

Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| BLOX FRUIT - Manajemen Stok & Penjualan
|--------------------------------------------------------------------------
*/
Route::prefix('bloxfruit')->name('bloxfruit.')->group(function () {
    Route::get('/', [BloxDashboard::class, 'index'])->name('dashboard');
    Route::get('search', [SearchController::class, 'index'])->name('search');

    // Master Buah
    Route::get('fruits', [BloxFruitController::class, 'index'])->name('fruits.index');
    Route::get('fruits/create', [BloxFruitController::class, 'create'])->name('fruits.create');
    Route::post('fruits', [BloxFruitController::class, 'store'])->name('fruits.store');
    Route::get('fruits/{fruit}/edit', [BloxFruitController::class, 'edit'])->name('fruits.edit');
    Route::put('fruits/{fruit}', [BloxFruitController::class, 'update'])->name('fruits.update');
    Route::delete('fruits/{fruit}', [BloxFruitController::class, 'destroy'])->name('fruits.destroy');

    // Master Skin Buah
    Route::get('skins', [FruitSkinController::class, 'index'])->name('skins.index');
    Route::get('skins/create', [FruitSkinController::class, 'create'])->name('skins.create');
    Route::post('skins', [FruitSkinController::class, 'store'])->name('skins.store');
    Route::get('skins/{skin}/edit', [FruitSkinController::class, 'edit'])->name('skins.edit');
    Route::put('skins/{skin}', [FruitSkinController::class, 'update'])->name('skins.update');
    Route::delete('skins/{skin}', [FruitSkinController::class, 'destroy'])->name('skins.destroy');

    // Master Gamepass
    Route::get('gamepasses', [GamepassController::class, 'index'])->name('gamepasses.index');
    Route::get('gamepasses/create', [GamepassController::class, 'create'])->name('gamepasses.create');
    Route::post('gamepasses', [GamepassController::class, 'store'])->name('gamepasses.store');
    Route::get('gamepasses/{gamepass}/edit', [GamepassController::class, 'edit'])->name('gamepasses.edit');
    Route::put('gamepasses/{gamepass}', [GamepassController::class, 'update'])->name('gamepasses.update');
    Route::delete('gamepasses/{gamepass}', [GamepassController::class, 'destroy'])->name('gamepasses.destroy');

    Route::get('permanents', [PermanentFruitPriceController::class, 'index'])->name('permanents.index');
    Route::get('permanents/create', [PermanentFruitPriceController::class, 'create'])->name('permanents.create');
    Route::post('permanents', [PermanentFruitPriceController::class, 'store'])->name('permanents.store');
    Route::get('permanents/{permanent}/edit', [PermanentFruitPriceController::class, 'edit'])->name('permanents.edit');
    Route::put('permanents/{permanent}', [PermanentFruitPriceController::class, 'update'])->name('permanents.update');
    Route::delete('permanents/{permanent}', [PermanentFruitPriceController::class, 'destroy'])->name('permanents.destroy');

    Route::get('joki-services', [JokiServiceController::class, 'index'])->name('joki-services.index');
    Route::get('joki-services/create', [JokiServiceController::class, 'create'])->name('joki-services.create');
    Route::post('joki-services', [JokiServiceController::class, 'store'])->name('joki-services.store');
    Route::get('joki-services/{joki_service}/edit', [JokiServiceController::class, 'edit'])->name('joki-services.edit');
    Route::put('joki-services/{joki_service}', [JokiServiceController::class, 'update'])->name('joki-services.update');
    Route::delete('joki-services/{joki_service}', [JokiServiceController::class, 'destroy'])->name('joki-services.destroy');

    // Akun Storage (penyimpanan stok)
    Route::get('storage', [StorageAccountController::class, 'index'])->name('storage.index');
    Route::get('storage/create', [StorageAccountController::class, 'create'])->name('storage.create');
    Route::post('storage', [StorageAccountController::class, 'store'])->name('storage.store');
    Route::get('storage/{storage}', [StorageAccountController::class, 'show'])->name('storage.show');
    Route::get('storage/{storage}/edit', [StorageAccountController::class, 'edit'])->name('storage.edit');
    Route::put('storage/{storage}', [StorageAccountController::class, 'update'])->name('storage.update');
    Route::delete('storage/{storage}', [StorageAccountController::class, 'destroy'])->name('storage.destroy');

    // Bulk save stok per akun storage
    Route::post('storage/{storage}/fruits', [StorageAccountController::class, 'bulkSaveFruitStock'])->name('storage.fruit.bulk');
    Route::post('storage/{storage}/gamepasses', [StorageAccountController::class, 'bulkSaveGamepassStock'])->name('storage.gamepass.bulk');
    Route::post('storage/{storage}/permanents', [StorageAccountController::class, 'bulkSavePermanentStock'])->name('storage.permanent.bulk');
    Route::post('storage/{storage}/skins', [StorageAccountController::class, 'bulkSaveSkinStock'])->name('storage.skin.bulk');
    Route::delete('storage/{storage}/clear', [StorageAccountController::class, 'clearStocks'])->name('storage.clear');
    Route::delete('storage-clear-all', [StorageAccountController::class, 'clearAllStocks'])->name('storage.clearAll');

    // Stok Akun Jual
    Route::get('accounts', [AccountStockController::class, 'index'])->name('accounts.index');
    Route::get('accounts/create', [AccountStockController::class, 'create'])->name('accounts.create');
    Route::post('accounts', [AccountStockController::class, 'store'])->name('accounts.store');
    Route::get('accounts/{account}/edit', [AccountStockController::class, 'edit'])->name('accounts.edit');
    Route::put('accounts/{account}', [AccountStockController::class, 'update'])->name('accounts.update');
    Route::delete('accounts/{account}', [AccountStockController::class, 'destroy'])->name('accounts.destroy');

    // Joki
    Route::get('joki', [JokiOrderController::class, 'index'])->name('joki.index');
    Route::get('joki/create', [JokiOrderController::class, 'create'])->name('joki.create');
    Route::post('joki', [JokiOrderController::class, 'store'])->name('joki.store');
    Route::get('joki/{joki}/edit', [JokiOrderController::class, 'edit'])->name('joki.edit');
    Route::put('joki/{joki}', [JokiOrderController::class, 'update'])->name('joki.update');
    Route::delete('joki/{joki}', [JokiOrderController::class, 'destroy'])->name('joki.destroy');
    Route::patch('joki/{joki}/status', [JokiOrderController::class, 'toggleStatus'])->name('joki.status');

    Route::get('profit', [ProfitController::class, 'index'])->name('profit.index');
    Route::get('profit/create', [ProfitController::class, 'create'])->name('profit.create');
    Route::post('profit', [ProfitController::class, 'store'])->name('profit.store');
    Route::get('profit/{profit}/edit', [ProfitController::class, 'edit'])->name('profit.edit');
    Route::put('profit/{profit}', [ProfitController::class, 'update'])->name('profit.update');
    Route::delete('profit/{profit}', [ProfitController::class, 'destroy'])->name('profit.destroy');
    Route::get('profit/trash', [ProfitController::class, 'trashed'])->name('profit.trash');
    Route::patch('profit/{slug}/restore', [ProfitController::class, 'restore'])->name('profit.restore');
    Route::post('profit/restore-all', [ProfitController::class, 'restoreAll'])->name('profit.restoreAll');
    Route::delete('profit/{slug}/force', [ProfitController::class, 'forceDelete'])->name('profit.forceDelete');
    Route::post('profit/wallet', [ProfitController::class, 'updateWallet'])->name('profit.wallet');
    Route::post('quick-sell', [QuickSellController::class, 'sell'])->name('quicksell');
    Route::get('rekap', [\App\Http\Controllers\BloxFruit\RekapController::class, 'index'])->name('rekap');
    Route::get('analisa-harga', [\App\Http\Controllers\BloxFruit\PriceAnalysisController::class, 'index'])->name('price-analysis');
});

/*
|--------------------------------------------------------------------------
| DIET TRACKER - Admin Panel & Monitoring
|--------------------------------------------------------------------------
*/
Route::prefix('diet')->name('diet.')->group(function () {
    Route::get('/', [DietDashboard::class, 'index'])->name('dashboard');
    Route::post('webhook/setup', [DietDashboard::class, 'setupWebhook'])->name('webhook.setup');
    Route::get('webhook/info', [DietDashboard::class, 'webhookInfo'])->name('webhook.info');
    Route::get('ai-logs', [AiLogController::class, 'index'])->name('ai-logs');

    // Users
    Route::get('users', [\App\Http\Controllers\DietTracker\UserController::class, 'index'])->name('users.index');
    Route::get('users/{profile}', [\App\Http\Controllers\DietTracker\UserController::class, 'show'])->name('users.show');
    Route::put('users/{profile}', [\App\Http\Controllers\DietTracker\UserController::class, 'update'])->name('users.update');
    Route::post('users/{profile}/recalculate', [\App\Http\Controllers\DietTracker\UserController::class, 'recalculate'])->name('users.recalculate');
    Route::post('users/{profile}/send-message', [\App\Http\Controllers\DietTracker\UserController::class, 'sendMessage'])->name('users.send-message');
    Route::post('users/{profile}/reset-data', [\App\Http\Controllers\DietTracker\UserController::class, 'resetData'])->name('users.reset-data');
    Route::delete('users/{profile}', [\App\Http\Controllers\DietTracker\UserController::class, 'destroy'])->name('users.destroy');
    Route::post('users-broadcast', [\App\Http\Controllers\DietTracker\UserController::class, 'broadcast'])->name('users.broadcast');

    // Statistik
    Route::get('stats', [\App\Http\Controllers\DietTracker\StatsController::class, 'index'])->name('stats');

    // Food Database CRUD
    Route::get('food-db', [\App\Http\Controllers\DietTracker\FoodDbController::class, 'index'])->name('food-db');
    Route::post('food-db', [\App\Http\Controllers\DietTracker\FoodDbController::class, 'store'])->name('food-db.store');
    Route::put('food-db/{food}', [\App\Http\Controllers\DietTracker\FoodDbController::class, 'update'])->name('food-db.update');
    Route::delete('food-db/{food}', [\App\Http\Controllers\DietTracker\FoodDbController::class, 'destroy'])->name('food-db.destroy');

    // API
    Route::get('api/foods', function (\Illuminate\Http\Request $request) {
        $q = $request->get('q', '');
        return \App\Models\DietTracker\FoodDatabase::where('nama', 'like', "%{$q}%")
            ->orderBy('nama')->limit(10)
            ->get(['id', 'nama', 'kategori', 'kalori', 'protein', 'karbohidrat', 'lemak', 'satuan_porsi']);
    })->name('api.foods');
});

// Settings
Route::get('/settings', [AuthController::class, 'showSettings'])->name('settings');
Route::post('/settings/password', [AuthController::class, 'updatePassword'])->name('settings.password');
Route::post('/settings/profile', [AuthController::class, 'updateProfile'])->name('settings.profile');
Route::post('/settings/backup', [AuthController::class, 'manualBackup'])->name('settings.backup');

// Store Settings (brand, kontak, marketing)
Route::get('/settings/store', [\App\Http\Controllers\StoreSettingsController::class, 'edit'])->name('settings.store.edit');
Route::post('/settings/store', [\App\Http\Controllers\StoreSettingsController::class, 'update'])->name('settings.store.update');

}); // End auth middleware
