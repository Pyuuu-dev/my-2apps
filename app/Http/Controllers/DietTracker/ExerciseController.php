<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\Exercise;
use App\Models\DietTracker\DietPlan;
use App\Models\DietTracker\ExerciseDatabase;
use App\Models\DietTracker\FastingLog;
use App\Services\DietHelperService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExerciseController extends Controller
{
    public function index(Request $request)
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create')->with('error', 'Buat program diet terlebih dahulu!');
        }

        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $exercises = Exercise::where('diet_plan_id', $planAktif->id)
            ->whereDate('tanggal', $tanggal)
            ->orderBy('created_at')
            ->get();

        $totalKalori = $exercises->sum('kalori_terbakar');
        $totalDurasi = $exercises->sum('durasi_menit');
        $jadwalMingguan = DietHelperService::jadwalOlahragaIdeal($planAktif->level_aktivitas, 'cut');
        $konsistensi = DietHelperService::hitungKonsistensi($planAktif);

        // Rekomendasi exercise dari database
        $exercisesByKategori = ExerciseDatabase::orderBy('nama')->get()->groupBy('kategori');

        // Cek puasa
        $puasaHariIni = FastingLog::where('diet_plan_id', $planAktif->id)
            ->whereDate('tanggal', $tanggal)->first();
        $configPuasa = $puasaHariIni ? DietHelperService::getConfigPuasa($puasaHariIni->tipe) : null;

        // Tanggal yang sudah ada data (30 hari terakhir)
        $tanggalAktif = Exercise::where('diet_plan_id', $planAktif->id)
            ->where('tanggal', '>=', Carbon::today()->subDays(30))
            ->selectRaw('DATE(tanggal) as tgl')
            ->groupBy('tgl')
            ->pluck('tgl')
            ->toArray();

        return view('diet.exercises.index', compact(
            'exercises', 'planAktif', 'tanggal', 'totalKalori', 'totalDurasi',
            'jadwalMingguan', 'konsistensi', 'exercisesByKategori',
            'puasaHariIni', 'configPuasa', 'tanggalAktif'
        ));
    }

    public function create()
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create')->with('error', 'Buat program diet terlebih dahulu!');
        }
        return view('diet.exercises.form', compact('planAktif'));
    }

    public function store(Request $request)
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create')->with('error', 'Buat program diet terlebih dahulu!');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis_olahraga' => 'required|string|max:255',
            'durasi_menit' => 'required|integer|min:1',
            'kalori_terbakar' => 'nullable|integer|min:0',
            'intensitas' => 'required|in:ringan,sedang,berat',
            'catatan' => 'nullable|string',
        ]);

        $validated['diet_plan_id'] = $planAktif->id;
        Exercise::create($validated);
        return redirect()->route('diet.exercises.index', ['tanggal' => $validated['tanggal']])->with('sukses', 'Olahraga berhasil dicatat!');
    }

    public function edit(Exercise $exercise)
    {
        $planAktif = DietPlan::getActivePlan();
        return view('diet.exercises.form', compact('exercise', 'planAktif'));
    }

    public function update(Request $request, Exercise $exercise)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis_olahraga' => 'required|string|max:255',
            'durasi_menit' => 'required|integer|min:1',
            'kalori_terbakar' => 'nullable|integer|min:0',
            'intensitas' => 'required|in:ringan,sedang,berat',
            'catatan' => 'nullable|string',
        ]);

        $exercise->update($validated);
        return redirect()->route('diet.exercises.index', ['tanggal' => $validated['tanggal']])->with('sukses', 'Olahraga berhasil diperbarui!');
    }

    public function destroy(Exercise $exercise)
    {
        $tanggal = $exercise->tanggal->format('Y-m-d');
        $exercise->delete();
        return redirect()->route('diet.exercises.index', ['tanggal' => $tanggal])->with('sukses', 'Olahraga berhasil dihapus!');
    }
}
