<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\Meal;
use App\Models\DietTracker\DietPlan;
use App\Models\DietTracker\FoodDatabase;
use App\Models\DietTracker\WaterLog;
use App\Models\DietTracker\FastingLog;
use App\Services\DietHelperService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MealController extends Controller
{
    public function index(Request $request)
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create')->with('error', 'Buat program diet terlebih dahulu!');
        }

        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $meals = Meal::where('diet_plan_id', $planAktif->id)
            ->whereDate('tanggal', $tanggal)
            ->orderByRaw("CASE waktu_makan WHEN 'sarapan' THEN 1 WHEN 'makan_siang' THEN 2 WHEN 'makan_malam' THEN 3 WHEN 'snack' THEN 4 END")
            ->get();

        $totalKalori = $meals->sum('kalori');

        // Cek puasa hari ini
        $puasaHariIni = FastingLog::where('diet_plan_id', $planAktif->id)
            ->whereDate('tanggal', $tanggal)->first();

        $puasaData = $puasaHariIni ? [
            'waktu_sahur' => $puasaHariIni->waktu_sahur,
            'waktu_berbuka' => $puasaHariIni->waktu_berbuka,
        ] : null;

        $jadwalIdeal = DietHelperService::jadwalMakanIdeal($planAktif->kalori_harian_target, $puasaData);
        $konsistensi = DietHelperService::hitungKonsistensi($planAktif);

        // Makanan grouped by kategori untuk quick add
        $foodsByKategori = FoodDatabase::orderBy('nama')->get()->groupBy('kategori');

        // Data minum hari ini
        $waterLogs = WaterLog::where('diet_plan_id', $planAktif->id)
            ->whereDate('tanggal', $tanggal)
            ->orderBy('created_at')
            ->get();
        $totalMinum = $waterLogs->sum('jumlah_ml');

        // Target air dari smart plan
        $smart = DietHelperService::generateSmartPlan(
            $planAktif->gender, $planAktif->umur, $planAktif->tinggi_cm,
            $planAktif->berat_sekarang ?? $planAktif->berat_awal, $planAktif->level_aktivitas
        );
        $targetAir = $smart['target_harian']['air_ml'];

        return view('diet.meals.index', compact(
            'meals', 'planAktif', 'tanggal', 'totalKalori', 'jadwalIdeal',
            'konsistensi', 'foodsByKategori', 'waterLogs', 'totalMinum', 'targetAir',
            'puasaHariIni'
        ));
    }

    public function create()
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create')->with('error', 'Buat program diet terlebih dahulu!');
        }
        $foods = FoodDatabase::orderBy('nama')->get();
        return view('diet.meals.form', compact('planAktif', 'foods'));
    }

    public function store(Request $request)
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create')->with('error', 'Buat program diet terlebih dahulu!');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'waktu_makan' => 'required|in:sarapan,makan_siang,makan_malam,snack',
            'nama_makanan' => 'required|string|max:255',
            'kalori' => 'nullable|integer|min:0',
            'protein' => 'nullable|numeric|min:0',
            'karbohidrat' => 'nullable|numeric|min:0',
            'lemak' => 'nullable|numeric|min:0',
            'porsi' => 'nullable|numeric|min:0.1',
            'catatan' => 'nullable|string',
        ]);

        $validated['diet_plan_id'] = $planAktif->id;
        Meal::create($validated);
        return redirect()->route('diet.meals.index', ['tanggal' => $validated['tanggal']])->with('sukses', $validated['nama_makanan'] . ' berhasil dicatat!');
    }

    /**
     * Quick add - langsung tambah dari database tanpa buka form
     */
    public function quickAdd(Request $request)
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create')->with('error', 'Buat program diet terlebih dahulu!');
        }

        $validated = $request->validate([
            'food_id' => 'required|exists:food_database,id',
            'waktu_makan' => 'required|in:sarapan,makan_siang,makan_malam,snack',
            'tanggal' => 'required|date',
            'porsi' => 'nullable|numeric|min:0.1',
        ]);

        $food = FoodDatabase::findOrFail($validated['food_id']);
        $porsi = $validated['porsi'] ?? 1;

        Meal::create([
            'diet_plan_id' => $planAktif->id,
            'tanggal' => $validated['tanggal'],
            'waktu_makan' => $validated['waktu_makan'],
            'nama_makanan' => $food->nama,
            'kalori' => (int) round($food->kalori * $porsi),
            'protein' => round($food->protein * $porsi, 1),
            'karbohidrat' => round($food->karbohidrat * $porsi, 1),
            'lemak' => round($food->lemak * $porsi, 1),
            'porsi' => $porsi,
        ]);

        return redirect()->route('diet.meals.index', ['tanggal' => $validated['tanggal']])
            ->with('sukses', $food->nama . ' (' . round($food->kalori * $porsi) . ' kkal) berhasil ditambahkan!');
    }

    public function edit(Meal $meal)
    {
        $planAktif = DietPlan::getActivePlan();
        $foods = FoodDatabase::orderBy('nama')->get();
        return view('diet.meals.form', compact('meal', 'planAktif', 'foods'));
    }

    public function update(Request $request, Meal $meal)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'waktu_makan' => 'required|in:sarapan,makan_siang,makan_malam,snack',
            'nama_makanan' => 'required|string|max:255',
            'kalori' => 'nullable|integer|min:0',
            'protein' => 'nullable|numeric|min:0',
            'karbohidrat' => 'nullable|numeric|min:0',
            'lemak' => 'nullable|numeric|min:0',
            'porsi' => 'nullable|numeric|min:0.1',
            'catatan' => 'nullable|string',
        ]);

        $meal->update($validated);
        return redirect()->route('diet.meals.index', ['tanggal' => $validated['tanggal']])->with('sukses', 'Makanan berhasil diperbarui!');
    }

    public function destroy(Meal $meal)
    {
        $tanggal = $meal->tanggal->format('Y-m-d');
        $meal->delete();
        return redirect()->route('diet.meals.index', ['tanggal' => $tanggal])->with('sukses', 'Makanan berhasil dihapus!');
    }
}
