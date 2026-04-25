<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\DailyActivity;
use App\Models\DietTracker\DietPlan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create')->with('error', 'Buat program diet terlebih dahulu!');
        }

        $tanggal = $request->get('tanggal');

        if ($tanggal) {
            // Filter by specific date
            $activities = DailyActivity::where('diet_plan_id', $planAktif->id)
                ->whereDate('tanggal', $tanggal)
                ->orderByDesc('tanggal')
                ->paginate(15);
        } else {
            $activities = DailyActivity::where('diet_plan_id', $planAktif->id)
                ->orderByDesc('tanggal')
                ->paginate(15);
        }

        $targetHarian = \App\Services\DietHelperService::targetAktivitasHarian($planAktif);
        $konsistensi = \App\Services\DietHelperService::hitungKonsistensi($planAktif);

        // Tanggal yang sudah ada data (30 hari terakhir)
        $tanggalAktif = DailyActivity::where('diet_plan_id', $planAktif->id)
            ->where('tanggal', '>=', Carbon::today()->subDays(30))
            ->selectRaw('DATE(tanggal) as tgl')
            ->groupBy('tgl')
            ->pluck('tgl')
            ->toArray();

        return view('diet.activities.index', compact('activities', 'planAktif', 'targetHarian', 'konsistensi', 'tanggalAktif', 'tanggal'));
    }

    public function create()
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create')->with('error', 'Buat program diet terlebih dahulu!');
        }
        return view('diet.activities.form', compact('planAktif'));
    }

    public function store(Request $request)
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'langkah_kaki' => 'nullable|integer|min:0',
            'jarak_km' => 'nullable|numeric|min:0',
            'kalori_terbakar' => 'nullable|integer|min:0',
            'berat_badan' => 'nullable|numeric|min:1',
            'jam_tidur' => 'nullable|integer|min:0|max:24',
            'air_minum_ml' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string',
        ]);

        $validated['diet_plan_id'] = $planAktif->id;

        // Update atau buat baru berdasarkan tanggal
        DailyActivity::updateOrCreate(
            ['diet_plan_id' => $planAktif->id, 'tanggal' => $validated['tanggal']],
            $validated
        );

        // Update berat sekarang di plan jika ada
        if (!empty($validated['berat_badan'])) {
            $planAktif->update(['berat_sekarang' => $validated['berat_badan']]);
        }

        return redirect()->route('diet.activities.index')->with('sukses', 'Aktivitas berhasil dicatat!');
    }

    public function edit(DailyActivity $activity)
    {
        $planAktif = DietPlan::getActivePlan();
        return view('diet.activities.form', compact('activity', 'planAktif'));
    }

    public function update(Request $request, DailyActivity $activity)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'langkah_kaki' => 'nullable|integer|min:0',
            'jarak_km' => 'nullable|numeric|min:0',
            'kalori_terbakar' => 'nullable|integer|min:0',
            'berat_badan' => 'nullable|numeric|min:1',
            'jam_tidur' => 'nullable|integer|min:0|max:24',
            'air_minum_ml' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string',
        ]);

        $activity->update($validated);

        if (!empty($validated['berat_badan'])) {
            $activity->dietPlan->update(['berat_sekarang' => $validated['berat_badan']]);
        }

        return redirect()->route('diet.activities.index')->with('sukses', 'Aktivitas berhasil diperbarui!');
    }

    public function destroy(DailyActivity $activity)
    {
        $activity->delete();
        return redirect()->route('diet.activities.index')->with('sukses', 'Aktivitas berhasil dihapus!');
    }
}
