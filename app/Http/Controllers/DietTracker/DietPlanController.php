<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\DietPlan;
use App\Services\DietHelperService;
use Illuminate\Http\Request;

class DietPlanController extends Controller
{
    public function index()
    {
        // Redirect ke dashboard (karena sekarang cuma 1 program)
        return redirect()->route('diet.dashboard');
    }

    public function create()
    {
        // Cek apakah sudah ada program
        if (DietPlan::hasPlan()) {
            return redirect()->route('diet.dashboard')->with('error', 'Anda sudah memiliki program diet. Edit program yang ada jika ingin mengubah target.');
        }
        return view('diet.plans.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gender' => 'required|in:pria,wanita',
            'umur' => 'required|integer|min:10|max:100',
            'tinggi_cm' => 'required|numeric|min:100|max:250',
            'berat_awal' => 'required|numeric|min:20|max:300',
            'level_aktivitas' => 'required|in:tidak_aktif,ringan,sedang,aktif,sangat_aktif',
        ]);

        // Smart auto-generate semua
        $smart = DietHelperService::generateSmartPlan(
            $validated['gender'],
            $validated['umur'],
            $validated['tinggi_cm'],
            $validated['berat_awal'],
            $validated['level_aktivitas']
        );

        $plan = DietPlan::create([
            'nama' => $validated['nama'],
            'gender' => $validated['gender'],
            'umur' => $validated['umur'],
            'tinggi_cm' => $validated['tinggi_cm'],
            'level_aktivitas' => $validated['level_aktivitas'],
            'berat_awal' => $validated['berat_awal'],
            'berat_target' => $smart['berat_target'],
            'berat_sekarang' => $validated['berat_awal'],
            'kalori_harian_target' => $smart['target_kalori'],
            'bmr' => $smart['bmr'],
            'tdee' => $smart['tdee'],
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => $smart['tanggal_selesai']->toDateString(),
            'catatan' => "Mode: " . DietHelperService::labelMode($smart['mode'])
                . " | BMI: " . $smart['bmi']['bmi'] . " (" . $smart['bmi']['kategori'] . ")"
                . " | Berat Ideal: " . $smart['berat_ideal'] . " kg"
                . " | Makro: P" . $smart['makro']['protein'] . "g K" . $smart['makro']['karbohidrat'] . "g L" . $smart['makro']['lemak'] . "g"
                . " | Air: " . number_format($smart['target_harian']['air_ml']) . "ml/hari"
                . " | Langkah: " . number_format($smart['target_harian']['langkah']) . "/hari"
                . " | Olahraga: " . $smart['target_harian']['olahraga_per_minggu'] . "x/minggu"
                . " | Tidur: " . $smart['target_harian']['tidur_jam'] . " jam/hari",
            'status' => 'aktif',
        ]);

        return redirect()->route('diet.dashboard')->with('sukses',
            'Program diet berhasil dibuat! BMI: ' . $smart['bmi']['bmi'] . ' (' . $smart['bmi']['kategori'] . ') - Target: ' . DietHelperService::labelMode($smart['mode']) . ' ke ' . $smart['berat_target'] . ' kg dalam ~' . $smart['minggu'] . ' minggu'
        );
    }

    public function edit(DietPlan $plan)
    {
        return view('diet.plans.form', compact('plan'));
    }

    public function update(Request $request, DietPlan $plan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gender' => 'required|in:pria,wanita',
            'umur' => 'required|integer|min:10|max:100',
            'tinggi_cm' => 'required|numeric|min:100|max:250',
            'berat_awal' => 'required|numeric|min:20|max:300',
            'level_aktivitas' => 'required|in:tidak_aktif,ringan,sedang,aktif,sangat_aktif',
            'status' => 'sometimes|in:aktif,selesai,berhenti',
        ]);

        $berat = $plan->berat_sekarang ?? $validated['berat_awal'];
        $smart = DietHelperService::generateSmartPlan(
            $validated['gender'],
            $validated['umur'],
            $validated['tinggi_cm'],
            $berat,
            $validated['level_aktivitas']
        );

        $plan->update([
            'nama' => $validated['nama'],
            'gender' => $validated['gender'],
            'umur' => $validated['umur'],
            'tinggi_cm' => $validated['tinggi_cm'],
            'level_aktivitas' => $validated['level_aktivitas'],
            'berat_awal' => $validated['berat_awal'],
            'berat_target' => $smart['berat_target'],
            'kalori_harian_target' => $smart['target_kalori'],
            'bmr' => $smart['bmr'],
            'tdee' => $smart['tdee'],
            'tanggal_selesai' => $smart['tanggal_selesai']->toDateString(),
            'status' => $validated['status'] ?? $plan->status,
        ]);

        return redirect()->route('diet.dashboard')->with('sukses', 'Program diet berhasil diperbarui!');
    }

    public function destroy(DietPlan $plan)
    {
        $plan->delete();
        return redirect()->route('diet.dashboard')->with('sukses', 'Program diet berhasil dihapus!');
    }
}
