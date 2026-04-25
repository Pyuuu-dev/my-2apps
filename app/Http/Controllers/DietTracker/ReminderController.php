<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\Reminder;
use App\Models\DietTracker\DietPlan;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    /**
     * Preset pengingat yang bisa dipilih, dikelompokkan per kategori
     */
    private function getPresets(): array
    {
        return [
            // === MAKAN ===
            ['judul' => 'Sarapan Pagi', 'waktu' => '07:00', 'tipe' => 'makan', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Waktunya sarapan! Mulai hari dengan makanan bergizi. Jangan skip sarapan ya, metabolisme butuh bahan bakar pagi.'],
            ['judul' => 'Snack Pagi', 'waktu' => '10:00', 'tipe' => 'makan', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Waktunya snack sehat! Pilih buah, yogurt, atau kacang-kacangan. Porsi kecil agar tidak kelaparan saat makan siang.'],
            ['judul' => 'Makan Siang', 'waktu' => '12:00', 'tipe' => 'makan', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Waktunya makan siang! Pilih menu seimbang: nasi/karbohidrat + protein (ayam/ikan/tahu) + sayur. Makan pelan-pelan ya.'],
            ['judul' => 'Snack Sore', 'waktu' => '15:00', 'tipe' => 'makan', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Snack sore! Pilih yang rendah kalori: buah potong, edamame, atau crackers gandum. Hindari gorengan.'],
            ['judul' => 'Makan Malam', 'waktu' => '18:30', 'tipe' => 'makan', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Waktunya makan malam! Kurangi karbohidrat, perbanyak protein dan sayur. Usahakan selesai makan sebelum jam 19:00.'],
            ['judul' => 'Stop Makan Malam', 'waktu' => '20:00', 'tipe' => 'makan', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Sudah lewat jam makan! Kalau lapar, minum air putih atau teh tanpa gula. Hindari ngemil malam hari.'],
            ['judul' => 'Catat Makanan', 'waktu' => '21:00', 'tipe' => 'makan', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Sudah catat semua makanan hari ini? Buka app dan pastikan semua tercatat agar tracking kalori akurat.'],
            ['judul' => 'Meal Prep Reminder', 'waktu' => '20:00', 'tipe' => 'makan', 'hari_aktif' => 'weekend',
             'pesan' => 'Siapkan meal prep untuk minggu depan! Masak protein, potong sayur, dan siapkan container. Hemat waktu dan kalori!'],

            // === MINUM AIR ===
            ['judul' => 'Minum Air Bangun Tidur', 'waktu' => '06:30', 'tipe' => 'minum', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Minum 1-2 gelas air putih setelah bangun tidur. Tubuh dehidrasi setelah 7-8 jam tidur, ini penting untuk metabolisme!'],
            ['judul' => 'Minum Air Pagi', 'waktu' => '08:30', 'tipe' => 'minum', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Sudah minum air pagi ini? Targetkan 2 gelas sebelum jam 9. Air putih membantu fokus dan energi.'],
            ['judul' => 'Minum Air Sebelum Makan Siang', 'waktu' => '11:30', 'tipe' => 'minum', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Minum 1 gelas air 30 menit sebelum makan siang. Ini membantu kontrol porsi dan mencegah makan berlebihan.'],
            ['judul' => 'Minum Air Siang', 'waktu' => '13:00', 'tipe' => 'minum', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Jangan lupa minum air setelah makan siang! Dehidrasi sering terasa seperti lapar. Minum dulu sebelum ngemil.'],
            ['judul' => 'Minum Air Sore', 'waktu' => '15:30', 'tipe' => 'minum', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Checkpoint sore! Sudah berapa gelas hari ini? Pastikan sudah minum minimal 6 gelas sebelum sore.'],
            ['judul' => 'Minum Air Sebelum Olahraga', 'waktu' => '16:30', 'tipe' => 'minum', 'hari_aktif' => 'senin_jumat',
             'pesan' => 'Mau olahraga? Minum 1-2 gelas air 30 menit sebelumnya. Tubuh yang terhidrasi performa lebih baik!'],
            ['judul' => 'Minum Air Malam', 'waktu' => '19:00', 'tipe' => 'minum', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Minum air terakhir hari ini! Cek progress - sudah berapa ml? Kejar target sebelum tidur.'],

            // === OLAHRAGA ===
            ['judul' => 'Olahraga Pagi', 'waktu' => '06:00', 'tipe' => 'olahraga', 'hari_aktif' => 'senin_jumat',
             'pesan' => 'Bangun dan bergerak! 20-30 menit olahraga pagi membakar lemak lebih efektif karena perut masih kosong.'],
            ['judul' => 'Stretching Pagi', 'waktu' => '07:00', 'tipe' => 'olahraga', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Stretching 5-10 menit setelah bangun tidur. Peregangan membantu sirkulasi darah dan mengurangi kaku badan.'],
            ['judul' => 'Jalan Kaki Siang', 'waktu' => '12:30', 'tipe' => 'olahraga', 'hari_aktif' => 'senin_jumat',
             'pesan' => 'Setelah makan siang, jalan kaki 10-15 menit. Ini membantu pencernaan dan membakar kalori ekstra.'],
            ['judul' => 'Olahraga Sore', 'waktu' => '17:00', 'tipe' => 'olahraga', 'hari_aktif' => 'senin_jumat',
             'pesan' => 'Waktunya workout sore! 30-45 menit cardio atau strength training. Konsistensi lebih penting dari intensitas.'],
            ['judul' => 'Olahraga Weekend', 'waktu' => '08:00', 'tipe' => 'olahraga', 'hari_aktif' => 'weekend',
             'pesan' => 'Weekend workout! Jogging, bersepeda, berenang, atau olahraga favorit kamu. Nikmati prosesnya!'],
            ['judul' => 'Naik Tangga', 'waktu' => '09:00', 'tipe' => 'olahraga', 'hari_aktif' => 'senin_jumat',
             'pesan' => 'Pilih tangga daripada lift hari ini! Naik tangga 5 menit membakar ~50 kalori dan menguatkan kaki.'],
            ['judul' => 'Plank Challenge', 'waktu' => '19:30', 'tipe' => 'olahraga', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Plank 1 menit sebelum tidur! Latihan core sederhana tapi efektif. Mulai dari 30 detik kalau belum kuat.'],
            ['judul' => 'Cardio Ringan', 'waktu' => '16:00', 'tipe' => 'olahraga', 'hari_aktif' => 'weekend',
             'pesan' => 'Sore weekend, waktunya cardio ringan! Jalan cepat 30 menit atau bersepeda santai di sekitar rumah.'],

            // === TIMBANG BADAN ===
            ['judul' => 'Timbang Badan Pagi', 'waktu' => '06:30', 'tipe' => 'timbang', 'hari_aktif' => 'weekend',
             'pesan' => 'Timbang berat badan pagi ini sebelum makan dan setelah ke toilet. Catat hasilnya di app!'],
            ['judul' => 'Timbang Mingguan', 'waktu' => '07:00', 'tipe' => 'timbang', 'hari_aktif' => 'weekend',
             'pesan' => 'Waktunya timbang mingguan! Ingat: fluktuasi 0.5-1 kg itu normal. Yang penting tren jangka panjang turun.'],
            ['judul' => 'Ukur Lingkar Perut', 'waktu' => '07:00', 'tipe' => 'timbang', 'hari_aktif' => 'weekend',
             'pesan' => 'Selain timbang, ukur juga lingkar perut! Kadang berat tidak turun tapi lingkar perut mengecil karena otot terbentuk.'],
            ['judul' => 'Update Progress Bulanan', 'waktu' => '09:00', 'tipe' => 'timbang', 'hari_aktif' => 'weekend',
             'pesan' => 'Sudah akhir bulan! Waktunya update progress bulanan di app. Timbang berat dan catat perkembangan bulan ini.'],

            // === TIDUR ===
            ['judul' => 'Matikan Layar', 'waktu' => '21:00', 'tipe' => 'tidur', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Mulai kurangi screen time! Blue light dari HP mengganggu produksi melatonin. Baca buku atau dengarkan musik.'],
            ['judul' => 'Persiapan Tidur', 'waktu' => '22:00', 'tipe' => 'tidur', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Waktunya bersiap tidur! Tidur 7-8 jam penting untuk recovery otot, kontrol hormon lapar, dan metabolisme.'],
            ['judul' => 'Waktunya Tidur', 'waktu' => '22:30', 'tipe' => 'tidur', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Letakkan HP dan tidur sekarang! Kurang tidur meningkatkan hormon ghrelin (lapar) dan menurunkan leptin (kenyang).'],
            ['judul' => 'Tidur Awal Weekend', 'waktu' => '23:00', 'tipe' => 'tidur', 'hari_aktif' => 'weekend',
             'pesan' => 'Weekend bukan alasan begadang! Tidur konsisten membantu ritme sirkadian dan kualitas diet kamu.'],

            // === MOTIVASI ===
            ['judul' => 'Motivasi Pagi', 'waktu' => '06:45', 'tipe' => 'makan', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Selamat pagi! Hari baru, kesempatan baru. Setiap pilihan makanan sehat hari ini membawa kamu lebih dekat ke target. Semangat!'],
            ['judul' => 'Reminder Tujuan', 'waktu' => '12:00', 'tipe' => 'timbang', 'hari_aktif' => 'senin_jumat',
             'pesan' => 'Ingat kenapa kamu mulai diet ini! Visualisasikan diri kamu di berat target. Setiap hari kecil menuju perubahan besar.'],
            ['judul' => 'Jangan Menyerah', 'waktu' => '15:00', 'tipe' => 'makan', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Sore ini mungkin terasa berat, tapi ingat: progress bukan perfection. Kalau slip, lanjutkan saja. Jangan menyerah!'],
            ['judul' => 'Refleksi Malam', 'waktu' => '20:30', 'tipe' => 'makan', 'hari_aktif' => 'setiap_hari',
             'pesan' => 'Apa 1 hal baik yang kamu lakukan untuk tubuhmu hari ini? Apresiasi diri sendiri. Konsistensi > kesempurnaan.'],
            ['judul' => 'Motivasi Senin', 'waktu' => '07:00', 'tipe' => 'olahraga', 'hari_aktif' => 'senin_jumat',
             'pesan' => 'Happy Monday! Minggu baru, semangat baru. Set target kecil minggu ini: olahraga 3x, minum air cukup, tidur tepat waktu.'],
            ['judul' => 'Cek Foto Before', 'waktu' => '08:00', 'tipe' => 'timbang', 'hari_aktif' => 'weekend',
             'pesan' => 'Ambil foto progress hari ini! Bandingkan dengan foto awal. Perubahan fisik kadang tidak terasa di cermin tapi terlihat di foto.'],
        ];
    }

    /**
     * Kelompokkan preset per kategori untuk UI
     */
    private function getPresetsByCategory(): array
    {
        $presets = $this->getPresets();
        $categories = [
            'makan' => ['label' => 'Makan & Nutrisi', 'icon' => '🍽', 'color' => 'orange', 'items' => []],
            'minum' => ['label' => 'Minum Air', 'icon' => '💧', 'color' => 'blue', 'items' => []],
            'olahraga' => ['label' => 'Olahraga & Aktivitas', 'icon' => '🏃', 'color' => 'red', 'items' => []],
            'timbang' => ['label' => 'Timbang & Progress', 'icon' => '⚖️', 'color' => 'purple', 'items' => []],
            'tidur' => ['label' => 'Tidur & Recovery', 'icon' => '😴', 'color' => 'indigo', 'items' => []],
        ];

        foreach ($presets as $i => $preset) {
            $preset['index'] = $i;
            $categories[$preset['tipe']]['items'][] = $preset;
        }

        return $categories;
    }

    public function index()
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create')->with('error', 'Buat program diet terlebih dahulu!');
        }

        $reminders = Reminder::where('diet_plan_id', $planAktif->id)
            ->orderBy('waktu')
            ->get();

        $telegram = new TelegramService();
        $telegramConfigured = $telegram->isConfigured();

        $presetCategories = $this->getPresetsByCategory();
        $presets = $this->getPresets();

        // Cek preset mana yang sudah ditambahkan
        $existingJudul = $reminders->pluck('judul')->toArray();

        return view('diet.reminders.index', compact('reminders', 'planAktif', 'telegramConfigured', 'presetCategories', 'presets', 'existingJudul'));
    }

    public function create()
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create')->with('error', 'Buat program diet terlebih dahulu!');
        }
        return view('diet.reminders.form', compact('planAktif'));
    }

    public function store(Request $request)
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'pesan' => 'nullable|string',
            'waktu' => 'required|date_format:H:i',
            'hari_aktif' => 'required|in:setiap_hari,senin_jumat,weekend,custom',
            'tipe' => 'required|in:makan,olahraga,minum,timbang,tidur',
        ]);

        $validated['diet_plan_id'] = $planAktif->id;
        Reminder::create($validated);
        return redirect()->route('diet.reminders.index')->with('sukses', 'Pengingat "' . $validated['judul'] . '" berhasil ditambahkan!');
    }

    /**
     * Tambah preset pengingat (batch)
     */
    public function addPreset(Request $request)
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create');
        }

        $presetIndex = $request->input('preset_index');
        $presets = $this->getPresets();

        if ($presetIndex === 'all') {
            // Tambah semua preset
            $added = 0;
            foreach ($presets as $preset) {
                $exists = Reminder::where('diet_plan_id', $planAktif->id)
                    ->where('judul', $preset['judul'])->exists();
                if (!$exists) {
                    Reminder::create(array_merge($preset, ['diet_plan_id' => $planAktif->id]));
                    $added++;
                }
            }
            return redirect()->route('diet.reminders.index')->with('sukses', "{$added} pengingat preset berhasil ditambahkan!");
        }

        if (isset($presets[$presetIndex])) {
            $preset = $presets[$presetIndex];
            $exists = Reminder::where('diet_plan_id', $planAktif->id)
                ->where('judul', $preset['judul'])->exists();
            if ($exists) {
                return redirect()->route('diet.reminders.index')->with('error', 'Pengingat "' . $preset['judul'] . '" sudah ada!');
            }
            Reminder::create(array_merge($preset, ['diet_plan_id' => $planAktif->id]));
            return redirect()->route('diet.reminders.index')->with('sukses', 'Pengingat "' . $preset['judul'] . '" berhasil ditambahkan!');
        }

        return redirect()->route('diet.reminders.index')->with('error', 'Preset tidak ditemukan.');
    }

    public function edit(Reminder $reminder)
    {
        $planAktif = DietPlan::getActivePlan();
        return view('diet.reminders.form', compact('reminder', 'planAktif'));
    }

    public function update(Request $request, Reminder $reminder)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'pesan' => 'nullable|string',
            'waktu' => 'required|date_format:H:i',
            'hari_aktif' => 'required|in:setiap_hari,senin_jumat,weekend,custom',
            'tipe' => 'required|in:makan,olahraga,minum,timbang,tidur',
            'aktif' => 'boolean',
        ]);

        $reminder->update($validated);
        return redirect()->route('diet.reminders.index')->with('sukses', 'Pengingat berhasil diperbarui!');
    }

    public function toggleAktif(Reminder $reminder)
    {
        $reminder->update(['aktif' => !$reminder->aktif]);
        return redirect()->route('diet.reminders.index');
    }

    public function destroy(Reminder $reminder)
    {
        $nama = $reminder->judul;
        $reminder->delete();
        return redirect()->route('diet.reminders.index')->with('sukses', 'Pengingat "' . $nama . '" berhasil dihapus!');
    }

    /**
     * Hapus semua pengingat
     */
    public function destroyAll()
    {
        $planAktif = DietPlan::getActivePlan();
        if (!$planAktif) {
            return redirect()->route('diet.plans.create');
        }

        $count = Reminder::where('diet_plan_id', $planAktif->id)->count();
        Reminder::where('diet_plan_id', $planAktif->id)->delete();

        return redirect()->route('diet.reminders.index')->with('sukses', $count . ' pengingat berhasil dihapus semua!');
    }

    /**
     * Test kirim pesan Telegram
     */
    public function testTelegram()
    {
        $telegram = new TelegramService();
        $result = $telegram->testConnection();

        return redirect()->route('diet.reminders.index')
            ->with($result['success'] ? 'sukses' : 'error', $result['message']);
    }

    /**
     * Simpan config Telegram
     */
    public function saveTelegramConfig(Request $request)
    {
        $validated = $request->validate([
            'bot_token' => 'required|string',
            'chat_id' => 'required|string',
        ]);

        // Update .env file
        $envPath = base_path('.env');
        $env = file_get_contents($envPath);

        $env = preg_replace('/^TELEGRAM_BOT_TOKEN=.*/m', 'TELEGRAM_BOT_TOKEN=' . $validated['bot_token'], $env);
        $env = preg_replace('/^TELEGRAM_CHAT_ID=.*/m', 'TELEGRAM_CHAT_ID=' . $validated['chat_id'], $env);

        file_put_contents($envPath, $env);

        // Clear config cache
        \Artisan::call('config:cache');

        return redirect()->route('diet.reminders.index')->with('sukses', 'Konfigurasi Telegram berhasil disimpan!');
    }
}
