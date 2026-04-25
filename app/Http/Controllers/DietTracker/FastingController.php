<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\DietPlan;
use App\Models\DietTracker\FastingLog;
use Illuminate\Http\Request;

class FastingController extends Controller
{
    /**
     * Toggle puasa hari ini (on/off)
     */
    public function toggle(Request $request)
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create');
        }

        $tanggal = $request->get('tanggal', now()->toDateString());
        $existing = FastingLog::where('diet_plan_id', $planAktif->id)
            ->whereDate('tanggal', $tanggal)->first();

        if ($existing) {
            $existing->delete();
            return redirect()->back()->with('sukses', 'Mode puasa dinonaktifkan untuk hari ini.');
        }

        FastingLog::create([
            'diet_plan_id' => $planAktif->id,
            'tanggal' => $tanggal,
            'tipe' => $request->get('tipe', 'sunnah'),
            'waktu_sahur' => $request->get('waktu_sahur', '04:00'),
            'waktu_berbuka' => $request->get('waktu_berbuka', '18:15'),
        ]);

        return redirect()->back()->with('sukses', 'Mode puasa diaktifkan! Jadwal makan & minum disesuaikan.');
    }

    /**
     * Set puasa dengan detail
     */
    public function store(Request $request)
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|string',
            'waktu_sahur' => 'required|date_format:H:i',
            'waktu_berbuka' => 'required|date_format:H:i',
            'catatan' => 'nullable|string',
        ]);

        $validated['diet_plan_id'] = $planAktif->id;

        FastingLog::updateOrCreate(
            ['diet_plan_id' => $planAktif->id, 'tanggal' => $validated['tanggal']],
            $validated
        );

        return redirect()->back()->with('sukses', 'Puasa ' . $validated['tanggal'] . ' berhasil disimpan!');
    }

    /**
     * Tandai puasa selesai/batal
     */
    public function complete(Request $request, FastingLog $fasting)
    {
        $fasting->update([
            'completed' => $request->boolean('completed', true),
        ]);

        $msg = $fasting->completed ? 'Puasa hari ini selesai! Alhamdulillah.' : 'Puasa dibatalkan.';
        return redirect()->back()->with('sukses', $msg);
    }
}
