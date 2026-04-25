<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
use App\Http\Controllers\DietTracker\DietPlanController;
use App\Http\Controllers\DietTracker\MealController;
use App\Http\Controllers\DietTracker\ExerciseController;
use App\Http\Controllers\DietTracker\ActivityController;
use App\Http\Controllers\DietTracker\ReminderController;
use App\Http\Controllers\DietTracker\MonthlyLogController;
use App\Http\Controllers\DietTracker\WaterLogController;
use App\Http\Controllers\DietTracker\FastingController;

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
| Protected Routes (Require Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

Route::get('/backup/download', [\App\Http\Controllers\BackupController::class, 'download'])->name('backup.download');
Route::post('/backup/telegram', [\App\Http\Controllers\BackupController::class, 'sendToTelegram'])->name('backup.telegram');
Route::post('/backup/config', [\App\Http\Controllers\BackupController::class, 'saveConfig'])->name('backup.config');
Route::post('/backup/test', [\App\Http\Controllers\BackupController::class, 'testBackupBot'])->name('backup.test');

Route::get('/', function () {
    // Blox Fruit stats
    $bfStats = [
        'total_buah' => \App\Models\BloxFruit\BloxFruit::count(),
        'total_skin' => \App\Models\BloxFruit\FruitSkin::count(),
        'total_akun' => \App\Models\BloxFruit\StorageAccount::count(),
        'total_joki' => \App\Models\BloxFruit\JokiOrder::count(),
        'joki_aktif' => \App\Models\BloxFruit\JokiOrder::whereIn('status', ['pending', 'proses'])->count(),
    ];

    // Diet stats
    $plan = \App\Models\DietTracker\DietPlan::getActivePlan();
    $dtStats = null;
    if ($plan) {
        $today = now()->toDateString();
        $kaloriMasuk = \App\Models\DietTracker\Meal::where('diet_plan_id', $plan->id)->whereDate('tanggal', $today)->sum('kalori');
        $totalMinum = \App\Models\DietTracker\WaterLog::where('diet_plan_id', $plan->id)->whereDate('tanggal', $today)->sum('jumlah_ml');
        $smart = \App\Services\DietHelperService::generateSmartPlan($plan->gender, $plan->umur, $plan->tinggi_cm, $plan->berat_sekarang ?? $plan->berat_awal, $plan->level_aktivitas);

        $dtStats = [
            'plan' => $plan,
            'kalori_masuk' => $kaloriMasuk,
            'target_kalori' => $plan->kalori_harian_target,
            'total_minum' => $totalMinum,
            'target_air' => $smart['target_harian']['air_ml'],
            'berat_sekarang' => $plan->berat_sekarang ?? $plan->berat_awal,
            'berat_target' => $plan->berat_target,
            'bmi' => $smart['bmi'],
        ];
    }

    return view('dashboard.index', compact('bfStats', 'dtStats'));
})->name('home');

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

    Route::get('profit', [ProfitController::class, 'index'])->name('profit.index');
    Route::get('profit/create', [ProfitController::class, 'create'])->name('profit.create');
    Route::post('profit', [ProfitController::class, 'store'])->name('profit.store');
    Route::get('profit/{profit}/edit', [ProfitController::class, 'edit'])->name('profit.edit');
    Route::put('profit/{profit}', [ProfitController::class, 'update'])->name('profit.update');
    Route::delete('profit/{profit}', [ProfitController::class, 'destroy'])->name('profit.destroy');
    Route::post('profit/wallet', [ProfitController::class, 'updateWallet'])->name('profit.wallet');
    Route::post('quick-sell', [QuickSellController::class, 'sell'])->name('quicksell');
});

/*
|--------------------------------------------------------------------------
| DIET TRACKER - Monitoring Diet & Kesehatan
|--------------------------------------------------------------------------
*/
Route::prefix('diet')->name('diet.')->group(function () {
    Route::get('/', [DietDashboard::class, 'index'])->name('dashboard');

    Route::get('plans', [DietPlanController::class, 'index'])->name('plans.index');
    Route::get('plans/create', [DietPlanController::class, 'create'])->name('plans.create');
    Route::post('plans', [DietPlanController::class, 'store'])->name('plans.store');
    Route::get('plans/{plan}/edit', [DietPlanController::class, 'edit'])->name('plans.edit');
    Route::put('plans/{plan}', [DietPlanController::class, 'update'])->name('plans.update');
    Route::delete('plans/{plan}', [DietPlanController::class, 'destroy'])->name('plans.destroy');
    Route::get('plans/{plan}/progress', [MonthlyLogController::class, 'show'])->name('plans.progress');
    Route::get('plans/{plan}/monthly', [MonthlyLogController::class, 'create'])->name('plans.monthly.create');
    Route::post('plans/{plan}/monthly', [MonthlyLogController::class, 'store'])->name('plans.monthly.store');
    Route::get('plans/{plan}/monthly/{log}/edit', [MonthlyLogController::class, 'edit'])->name('plans.monthly.edit');
    Route::put('plans/{plan}/monthly/{log}', [MonthlyLogController::class, 'update'])->name('plans.monthly.update');
    Route::delete('plans/{plan}/monthly/{log}', [MonthlyLogController::class, 'destroy'])->name('plans.monthly.destroy');

    Route::get('meals', [MealController::class, 'index'])->name('meals.index');
    Route::get('meals/create', [MealController::class, 'create'])->name('meals.create');
    Route::post('meals', [MealController::class, 'store'])->name('meals.store');
    Route::post('meals/quick', [MealController::class, 'quickAdd'])->name('meals.quick');
    Route::get('meals/{meal}/edit', [MealController::class, 'edit'])->name('meals.edit');
    Route::put('meals/{meal}', [MealController::class, 'update'])->name('meals.update');
    Route::delete('meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');

    Route::post('water', [WaterLogController::class, 'store'])->name('water.store');
    Route::delete('water/{waterLog}', [WaterLogController::class, 'destroy'])->name('water.destroy');
    Route::post('water/reset', [WaterLogController::class, 'reset'])->name('water.reset');

    Route::post('fasting/toggle', [FastingController::class, 'toggle'])->name('fasting.toggle');
    Route::post('fasting', [FastingController::class, 'store'])->name('fasting.store');
    Route::patch('fasting/{fasting}/complete', [FastingController::class, 'complete'])->name('fasting.complete');

    Route::get('exercises', [ExerciseController::class, 'index'])->name('exercises.index');
    Route::get('exercises/create', [ExerciseController::class, 'create'])->name('exercises.create');
    Route::post('exercises', [ExerciseController::class, 'store'])->name('exercises.store');
    Route::get('exercises/{exercise}/edit', [ExerciseController::class, 'edit'])->name('exercises.edit');
    Route::put('exercises/{exercise}', [ExerciseController::class, 'update'])->name('exercises.update');
    Route::delete('exercises/{exercise}', [ExerciseController::class, 'destroy'])->name('exercises.destroy');

    Route::get('activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('activities/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::get('activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
    Route::put('activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

    Route::get('reminders', [ReminderController::class, 'index'])->name('reminders.index');
    Route::get('reminders/create', [ReminderController::class, 'create'])->name('reminders.create');
    Route::post('reminders', [ReminderController::class, 'store'])->name('reminders.store');
    Route::get('reminders/{reminder}/edit', [ReminderController::class, 'edit'])->name('reminders.edit');
    Route::put('reminders/{reminder}', [ReminderController::class, 'update'])->name('reminders.update');
    Route::patch('reminders/{reminder}/toggle', [ReminderController::class, 'toggleAktif'])->name('reminders.toggle');
    Route::delete('reminders/destroy-all', [ReminderController::class, 'destroyAll'])->name('reminders.destroyAll');
    Route::delete('reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');
    Route::post('reminders/preset', [ReminderController::class, 'addPreset'])->name('reminders.preset');
    Route::post('reminders/telegram/test', [ReminderController::class, 'testTelegram'])->name('reminders.telegram.test');
    Route::post('reminders/telegram/config', [ReminderController::class, 'saveTelegramConfig'])->name('reminders.telegram.config');

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

}); // End auth middleware
