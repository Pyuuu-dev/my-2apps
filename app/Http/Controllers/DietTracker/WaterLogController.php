<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\DietPlan;
use App\Models\DietTracker\WaterLog;
use App\Services\DietHelperService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WaterLogController extends Controller
{
    /**
     * Tambah minum (1 gelas = 250ml default)
     */
    public function store(Request $request)
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create');
        }

        $validated = $request->validate([
            'jumlah_ml' => 'required|integer|min:50|max:2000',
            'tanggal' => 'nullable|date',
        ]);

        $tanggal = $validated['tanggal'] ?? now()->toDateString();

        // Tentukan waktu berdasarkan jam
        $jam = now()->hour;
        if ($jam < 10) $waktu = 'pagi';
        elseif ($jam < 14) $waktu = 'siang';
        elseif ($jam < 18) $waktu = 'sore';
        else $waktu = 'malam';

        WaterLog::create([
            'diet_plan_id' => $planAktif->id,
            'tanggal' => $tanggal,
            'jumlah_ml' => $validated['jumlah_ml'],
            'waktu' => $waktu,
        ]);

        $totalHariIni = WaterLog::where('diet_plan_id', $planAktif->id)
            ->whereDate('tanggal', $tanggal)
            ->sum('jumlah_ml');

        return redirect()->back()->with('sukses', '+' . $validated['jumlah_ml'] . 'ml - Total hari ini: ' . number_format($totalHariIni) . 'ml');
    }

    /**
     * Hapus catatan minum terakhir (undo)
     */
    public function destroy(WaterLog $waterLog)
    {
        $waterLog->delete();
        return redirect()->back()->with('sukses', 'Catatan minum dihapus');
    }

    /**
     * Reset minum hari ini
     */
    public function reset(Request $request)
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create');
        }

        $tanggal = $request->get('tanggal', now()->toDateString());

        WaterLog::where('diet_plan_id', $planAktif->id)
            ->whereDate('tanggal', $tanggal)
            ->delete();

        return redirect()->back()->with('sukses', 'Catatan minum hari ini direset');
    }
}
