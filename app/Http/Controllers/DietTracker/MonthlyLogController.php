<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\DietPlan;
use App\Models\DietTracker\MonthlyLog;
use App\Models\DietTracker\Meal;
use App\Models\DietTracker\Exercise;
use App\Models\DietTracker\DailyActivity;
use App\Services\DietHelperService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MonthlyLogController extends Controller
{
    /**
     * Tampilkan history bulanan
     */
    public function show(DietPlan $plan)
    {
        $logs = $plan->monthlyLogs()->orderByDesc('bulan')->get();
        $currentMonth = now()->format('Y-m');
        $currentLog = $logs->firstWhere('bulan', $currentMonth);

        // Hitung stats bulan ini secara live
        $startOfMonth = now()->startOfMonth();
        $today = now();
        $hariDiBulan = now()->daysInMonth;
        $hariLewat = $startOfMonth->diffInDays($today) + 1;

        $kaloriMasukBulanIni = Meal::where('diet_plan_id', $plan->id)
            ->whereBetween('tanggal', [$startOfMonth, $today])
            ->avg('kalori') ?? 0;

        $kaloriKeluarBulanIni = Exercise::where('diet_plan_id', $plan->id)
            ->whereBetween('tanggal', [$startOfMonth, $today])
            ->avg('kalori_terbakar') ?? 0;

        $hariOlahraga = Exercise::where('diet_plan_id', $plan->id)
            ->whereBetween('tanggal', [$startOfMonth, $today])
            ->distinct('tanggal')->count('tanggal');

        $hariCatat = Meal::where('diet_plan_id', $plan->id)
            ->whereBetween('tanggal', [$startOfMonth, $today])
            ->distinct('tanggal')->count('tanggal');

        $liveStats = [
            'bulan' => $currentMonth,
            'hari_lewat' => $hariLewat,
            'hari_di_bulan' => $hariDiBulan,
            'avg_kalori_masuk' => (int) round($kaloriMasukBulanIni),
            'avg_kalori_keluar' => (int) round($kaloriKeluarBulanIni),
            'hari_olahraga' => $hariOlahraga,
            'hari_catat' => $hariCatat,
            'konsistensi' => $hariLewat > 0 ? (int) round(($hariCatat / $hariLewat) * 100) : 0,
        ];

        // Bulan-bulan yang sudah ada log (untuk cegah duplikat)
        $bulanSudahAda = $logs->pluck('bulan')->toArray();

        return view('diet.plans.progress', compact('plan', 'logs', 'liveStats', 'currentLog', 'bulanSudahAda'));
    }

    /**
     * Form tambah progress bulanan (bisa pilih bulan)
     */
    public function create(DietPlan $plan, Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));
        $existing = MonthlyLog::where('diet_plan_id', $plan->id)->where('bulan', $bulan)->first();

        // Berat awal bulan = berat akhir bulan sebelumnya, atau berat sekarang
        $lastLog = $plan->monthlyLogs()->where('bulan', '<', $bulan)->orderByDesc('bulan')->first();
        $beratAwalBulan = $lastLog?->berat_akhir_bulan ?? $plan->berat_sekarang ?? $plan->berat_awal;

        // Bulan-bulan yang sudah ada log
        $bulanSudahAda = $plan->monthlyLogs()->pluck('bulan')->toArray();

        // Generate list bulan dari tanggal mulai sampai sekarang
        $start = $plan->tanggal_mulai->copy()->startOfMonth();
        $end = now()->startOfMonth();
        $daftarBulan = [];
        while ($start->lte($end)) {
            $key = $start->format('Y-m');
            $daftarBulan[$key] = $start->translatedFormat('F Y');
            $start->addMonth();
        }

        return view('diet.plans.monthly-form', compact('plan', 'bulan', 'existing', 'beratAwalBulan', 'bulanSudahAda', 'daftarBulan'));
    }

    /**
     * Simpan progress bulanan
     */
    public function store(Request $request, DietPlan $plan)
    {
        $validated = $request->validate([
            'bulan' => 'required|string',
            'berat_akhir_bulan' => 'required|numeric|min:20|max:300',
            'catatan' => 'nullable|string',
        ]);

        $bulan = $validated['bulan'];
        $startOfMonth = Carbon::parse($bulan . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($bulan . '-01')->endOfMonth();
        $today = now()->lt($endOfMonth) ? now() : $endOfMonth;
        $hariLewat = $startOfMonth->diffInDays($today) + 1;

        // Hitung stats bulan ini dari data harian
        $avgKaloriMasuk = (int) round(Meal::where('diet_plan_id', $plan->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->avg('kalori') ?? 0);
        $avgKaloriKeluar = (int) round(Exercise::where('diet_plan_id', $plan->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->avg('kalori_terbakar') ?? 0);
        $hariOlahraga = Exercise::where('diet_plan_id', $plan->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->distinct('tanggal')->count('tanggal');
        $hariCatat = Meal::where('diet_plan_id', $plan->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->distinct('tanggal')->count('tanggal');

        // Hitung stats aktivitas harian
        $activityStats = $this->hitungActivityStats($plan->id, $startOfMonth, $endOfMonth);

        // Berat awal bulan
        $lastLog = $plan->monthlyLogs()->where('bulan', '<', $bulan)->orderByDesc('bulan')->first();
        $beratAwalBulan = $lastLog?->berat_akhir_bulan ?? $plan->berat_sekarang ?? $plan->berat_awal;

        $beratTurun = round($beratAwalBulan - $validated['berat_akhir_bulan'], 1);

        MonthlyLog::updateOrCreate(
            ['diet_plan_id' => $plan->id, 'bulan' => $bulan],
            [
                'berat_awal_bulan' => $beratAwalBulan,
                'berat_akhir_bulan' => $validated['berat_akhir_bulan'],
                'berat_turun' => $beratTurun,
                'target_kalori' => $plan->kalori_harian_target,
                'avg_kalori_masuk' => $avgKaloriMasuk,
                'avg_kalori_keluar' => $avgKaloriKeluar,
                'total_hari_olahraga' => $hariOlahraga,
                'total_hari_catat' => $hariCatat,
                'konsistensi_persen' => $hariLewat > 0 ? (int) round(($hariCatat / $hariLewat) * 100) : 0,
                'avg_langkah' => $activityStats['avg_langkah'],
                'avg_tidur' => $activityStats['avg_tidur'],
                'avg_air_minum' => $activityStats['avg_air_minum'],
                'total_hari_aktivitas' => $activityStats['total_hari'],
                'catatan' => $validated['catatan'],
                'status' => 'selesai',
            ]
        );

        // Update berat sekarang di plan (hanya jika bulan ini atau bulan terbaru)
        $latestLog = $plan->monthlyLogs()->orderByDesc('bulan')->first();
        if ($latestLog) {
            $plan->update(['berat_sekarang' => $latestLog->berat_akhir_bulan]);

            // Recalculate target berdasarkan berat terbaru
            $smart = DietHelperService::generateSmartPlan(
                $plan->gender, $plan->umur, $plan->tinggi_cm,
                $latestLog->berat_akhir_bulan, $plan->level_aktivitas
            );
            $plan->update([
                'kalori_harian_target' => $smart['target_kalori'],
                'bmr' => $smart['bmr'],
                'tdee' => $smart['tdee'],
            ]);
        }

        return redirect()->route('diet.plans.progress', $plan)
            ->with('sukses', 'Progress ' . Carbon::parse($bulan . '-01')->translatedFormat('F Y') . ' berhasil disimpan! Berat: ' . $validated['berat_akhir_bulan'] . ' kg (' . ($beratTurun >= 0 ? '-' : '+') . abs($beratTurun) . ' kg)');
    }

    /**
     * Form edit progress bulanan
     */
    public function edit(DietPlan $plan, MonthlyLog $log)
    {
        $bulan = $log->bulan;
        $existing = $log;

        $lastLog = $plan->monthlyLogs()->where('bulan', '<', $bulan)->orderByDesc('bulan')->first();
        $beratAwalBulan = $lastLog?->berat_akhir_bulan ?? $plan->berat_awal;

        $bulanSudahAda = $plan->monthlyLogs()->where('id', '!=', $log->id)->pluck('bulan')->toArray();

        $start = $plan->tanggal_mulai->copy()->startOfMonth();
        $end = now()->startOfMonth();
        $daftarBulan = [];
        while ($start->lte($end)) {
            $key = $start->format('Y-m');
            $daftarBulan[$key] = $start->translatedFormat('F Y');
            $start->addMonth();
        }

        $isEdit = true;

        return view('diet.plans.monthly-form', compact('plan', 'bulan', 'existing', 'beratAwalBulan', 'bulanSudahAda', 'daftarBulan', 'isEdit', 'log'));
    }

    /**
     * Update progress bulanan
     */
    public function update(Request $request, DietPlan $plan, MonthlyLog $log)
    {
        $validated = $request->validate([
            'bulan' => 'required|string',
            'berat_akhir_bulan' => 'required|numeric|min:20|max:300',
            'catatan' => 'nullable|string',
        ]);

        $bulan = $validated['bulan'];
        $startOfMonth = Carbon::parse($bulan . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($bulan . '-01')->endOfMonth();
        $today = now()->lt($endOfMonth) ? now() : $endOfMonth;
        $hariLewat = $startOfMonth->diffInDays($today) + 1;

        $avgKaloriMasuk = (int) round(Meal::where('diet_plan_id', $plan->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->avg('kalori') ?? 0);
        $avgKaloriKeluar = (int) round(Exercise::where('diet_plan_id', $plan->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->avg('kalori_terbakar') ?? 0);
        $hariOlahraga = Exercise::where('diet_plan_id', $plan->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->distinct('tanggal')->count('tanggal');
        $hariCatat = Meal::where('diet_plan_id', $plan->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->distinct('tanggal')->count('tanggal');

        $activityStats = $this->hitungActivityStats($plan->id, $startOfMonth, $endOfMonth);

        $lastLog = $plan->monthlyLogs()->where('bulan', '<', $bulan)->where('id', '!=', $log->id)->orderByDesc('bulan')->first();
        $beratAwalBulan = $lastLog?->berat_akhir_bulan ?? $plan->berat_awal;

        $beratTurun = round($beratAwalBulan - $validated['berat_akhir_bulan'], 1);

        $log->update([
            'bulan' => $bulan,
            'berat_awal_bulan' => $beratAwalBulan,
            'berat_akhir_bulan' => $validated['berat_akhir_bulan'],
            'berat_turun' => $beratTurun,
            'target_kalori' => $plan->kalori_harian_target,
            'avg_kalori_masuk' => $avgKaloriMasuk,
            'avg_kalori_keluar' => $avgKaloriKeluar,
            'total_hari_olahraga' => $hariOlahraga,
            'total_hari_catat' => $hariCatat,
            'konsistensi_persen' => $hariLewat > 0 ? (int) round(($hariCatat / $hariLewat) * 100) : 0,
            'avg_langkah' => $activityStats['avg_langkah'],
            'avg_tidur' => $activityStats['avg_tidur'],
            'avg_air_minum' => $activityStats['avg_air_minum'],
            'total_hari_aktivitas' => $activityStats['total_hari'],
            'catatan' => $validated['catatan'],
        ]);

        // Update berat sekarang berdasarkan log terbaru
        $latestLog = $plan->monthlyLogs()->orderByDesc('bulan')->first();
        if ($latestLog) {
            $plan->update(['berat_sekarang' => $latestLog->berat_akhir_bulan]);
            $smart = DietHelperService::generateSmartPlan(
                $plan->gender, $plan->umur, $plan->tinggi_cm,
                $latestLog->berat_akhir_bulan, $plan->level_aktivitas
            );
            $plan->update([
                'kalori_harian_target' => $smart['target_kalori'],
                'bmr' => $smart['bmr'],
                'tdee' => $smart['tdee'],
            ]);
        }

        return redirect()->route('diet.plans.progress', $plan)
            ->with('sukses', 'Progress ' . Carbon::parse($bulan . '-01')->translatedFormat('F Y') . ' berhasil diperbarui!');
    }

    /**
     * Hapus progress bulanan
     */
    public function destroy(DietPlan $plan, MonthlyLog $log)
    {
        $bulanLabel = Carbon::parse($log->bulan . '-01')->translatedFormat('F Y');
        $log->delete();

        // Update berat sekarang berdasarkan log terbaru
        $latestLog = $plan->monthlyLogs()->orderByDesc('bulan')->first();
        if ($latestLog) {
            $plan->update(['berat_sekarang' => $latestLog->berat_akhir_bulan]);
        } else {
            $plan->update(['berat_sekarang' => $plan->berat_awal]);
        }

        return redirect()->route('diet.plans.progress', $plan)
            ->with('sukses', 'Progress ' . $bulanLabel . ' berhasil dihapus!');
    }

    /**
     * Hitung stats aktivitas harian untuk periode tertentu
     */
    private function hitungActivityStats(int $planId, $startDate, $endDate): array
    {
        $activities = DailyActivity::where('diet_plan_id', $planId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $totalHari = $activities->count();

        return [
            'avg_langkah' => $totalHari > 0 ? (int) round($activities->avg('langkah_kaki')) : 0,
            'avg_tidur' => $totalHari > 0 ? round($activities->avg('jam_tidur'), 1) : 0,
            'avg_air_minum' => $totalHari > 0 ? (int) round($activities->avg('air_minum_ml')) : 0,
            'total_hari' => $totalHari,
        ];
    }
}
