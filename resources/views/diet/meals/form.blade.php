@extends('layouts.app')
@section('title', isset($meal) ? 'Edit Makanan' : 'Tambah Makanan')

@section('content')
<div class="max-w-2xl mx-auto" x-data="mealCombo()">
    <form method="POST" action="{{ isset($meal) ? route('diet.meals.update', $meal) : route('diet.meals.store') }}" class="rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden">
        @csrf
        @if(isset($meal)) @method('PUT') @endif

        <div class="p-5 border-b border-gray-100">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', isset($meal) ? $meal->tanggal->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Makan</label>
                    <select name="waktu_makan" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        @foreach(['sarapan' => 'Sarapan', 'makan_siang' => 'Makan Siang', 'makan_malam' => 'Makan Malam', 'snack' => 'Snack'] as $k => $v)
                        <option value="{{ $k }}" {{ old('waktu_makan', $meal->waktu_makan ?? '') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if(!isset($meal))
        {{-- PILIH MAKANAN - klik untuk tambah ke keranjang --}}
        <div class="p-5 border-b border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-gray-900">Pilih Makanan <span class="text-gray-400 font-normal">(klik untuk tambah)</span></p>
                <input type="text" x-model="search" placeholder="Cari..." class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 w-40">
            </div>

            {{-- Kategori tabs --}}
            <div class="flex gap-1 mb-3 overflow-x-auto">
                <button type="button" @click="filterKat = ''" :class="filterKat === '' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="rounded-full px-3 py-1 text-[11px] font-medium whitespace-nowrap">Semua</button>
                <button type="button" @click="filterKat = 'sarapan'" :class="filterKat === 'sarapan' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="rounded-full px-3 py-1 text-[11px] font-medium whitespace-nowrap">Sarapan</button>
                <button type="button" @click="filterKat = 'makan_utama'" :class="filterKat === 'makan_utama' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="rounded-full px-3 py-1 text-[11px] font-medium whitespace-nowrap">Makan Utama</button>
                <button type="button" @click="filterKat = 'snack'" :class="filterKat === 'snack' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="rounded-full px-3 py-1 text-[11px] font-medium whitespace-nowrap">Snack</button>
                <button type="button" @click="filterKat = 'minuman'" :class="filterKat === 'minuman' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="rounded-full px-3 py-1 text-[11px] font-medium whitespace-nowrap">Minuman</button>
            </div>

            {{-- Food grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5 max-h-64 overflow-y-auto pr-1">
                <template x-for="food in filteredFoods" :key="food.id">
                    <button type="button" @click="addItem(food)"
                        :class="isSelected(food.id) ? 'border-emerald-400 bg-emerald-50 ring-1 ring-emerald-400' : 'border-gray-100 bg-white hover:border-emerald-200 hover:bg-emerald-50/50'"
                        class="rounded-lg border p-2 text-left transition-all">
                        <p class="text-sm font-medium text-gray-800 truncate" x-text="food.nama"></p>
                        <p class="text-[11px] text-gray-400" x-text="food.kalori + ' kkal &middot; ' + food.porsi"></p>
                    </button>
                </template>
            </div>

            {{-- Makanan tidak ada di daftar --}}
            <div class="mt-3 pt-3 border-t border-gray-100" x-data="{ showManual: false }">
                <button type="button" @click="showManual = !showManual" class="text-sm text-emerald-600 font-medium hover:text-emerald-800">
                    <span x-text="showManual ? '- Tutup input manual' : '+ Makanan tidak ada di daftar?'"></span>
                </button>
                <div x-show="showManual" x-transition class="mt-3 rounded-xl bg-amber-50 border border-amber-100 p-3">
                    <p class="text-[11px] text-amber-700 mb-2">Ketik nama dan perkiraan kalori, lalu klik Tambah.</p>
                    <div class="grid grid-cols-5 gap-2">
                        <div class="col-span-2">
                            <input type="text" x-ref="manualNama" placeholder="Nama makanan" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div>
                            <input type="number" x-ref="manualKalori" placeholder="Kalori" min="0" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div>
                            <input type="number" x-ref="manualProtein" placeholder="Protein" min="0" step="0.1" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div>
                            <button type="button" @click="addManual()" class="w-full rounded-lg bg-amber-500 px-3 py-2 text-sm font-medium text-white hover:bg-amber-600">Tambah</button>
                        </div>
                    </div>
                    <p class="text-[11px] text-amber-600 mt-1.5">Tidak tahu kalorinya? Perkiraan: nasi 1 piring ~200, lauk goreng ~200, sayur ~60, gorengan ~150, buah ~80</p>
                </div>
            </div>
        </div>

        {{-- KERANJANG - makanan yang dipilih --}}
        <div class="p-5 border-b border-gray-100" x-show="items.length > 0" x-transition>
            <p class="text-sm font-semibold text-gray-900 mb-3">Menu Dipilih <span class="text-emerald-600" x-text="'(' + items.length + ' item)'"></span></p>
            <div class="space-y-2">
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="flex items-center gap-3 rounded-lg p-2.5" :class="item.manual ? 'bg-amber-50' : 'bg-gray-50'">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                <span x-text="item.nama"></span>
                                <span x-show="item.manual" class="text-[10px] text-amber-500 font-normal">(manual)</span>
                            </p>
                            <p class="text-[11px] text-gray-400" x-text="'P:' + r(item.protein * item.porsi) + 'g K:' + r(item.karbo * item.porsi) + 'g L:' + r(item.lemak * item.porsi) + 'g'"></p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <div class="flex items-center gap-1">
                                <button type="button" @click="item.porsi = Math.max(0.5, item.porsi - 0.5); recalc()" class="rounded bg-gray-200 w-6 h-6 text-xs font-bold text-gray-600 hover:bg-gray-300">-</button>
                                <span class="text-sm font-bold w-8 text-center" x-text="item.porsi"></span>
                                <button type="button" @click="item.porsi += 0.5; recalc()" class="rounded bg-gray-200 w-6 h-6 text-xs font-bold text-gray-600 hover:bg-gray-300">+</button>
                            </div>
                            <span class="text-sm font-bold text-orange-600 w-14 text-right" x-text="Math.round(item.kalori * item.porsi)"></span>
                            <button type="button" @click="items.splice(idx, 1); recalc()" class="text-gray-400 hover:text-red-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        @endif

        {{-- TOTAL NUTRISI --}}
        <div class="p-5 bg-gradient-to-r from-emerald-50 to-teal-50">
            <p class="text-xs font-semibold text-emerald-700 mb-3" x-text="items.length > 1 ? 'Total Nutrisi (' + items.length + ' item digabung)' : 'Nutrisi'"></p>
            <div class="grid grid-cols-4 gap-3 text-center mb-4">
                <div class="rounded-lg bg-white p-2">
                    <p class="text-lg font-extrabold text-orange-600" x-text="totalKalori">0</p>
                    <p class="text-[11px] text-gray-400">Kalori</p>
                </div>
                <div class="rounded-lg bg-white p-2">
                    <p class="text-lg font-extrabold text-blue-600" x-text="totalProtein + 'g'">0g</p>
                    <p class="text-[11px] text-gray-400">Protein</p>
                </div>
                <div class="rounded-lg bg-white p-2">
                    <p class="text-lg font-extrabold text-amber-600" x-text="totalKarbo + 'g'">0g</p>
                    <p class="text-[11px] text-gray-400">Karbo</p>
                </div>
                <div class="rounded-lg bg-white p-2">
                    <p class="text-lg font-extrabold text-pink-600" x-text="totalLemak + 'g'">0g</p>
                    <p class="text-[11px] text-gray-400">Lemak</p>
                </div>
            </div>

            {{-- Hidden inputs --}}
            <input type="hidden" name="nama_makanan" :value="namaGabungan">
            <input type="hidden" name="kalori" :value="totalKalori">
            <input type="hidden" name="protein" :value="totalProtein">
            <input type="hidden" name="karbohidrat" :value="totalKarbo">
            <input type="hidden" name="lemak" :value="totalLemak">
            <input type="hidden" name="porsi" value="1">

            @if(isset($meal))
            {{-- Edit mode: manual fields --}}
            <div class="space-y-3 mb-4">
                <div>
                    <label class="block text-[11px] text-gray-600 mb-0.5">Nama Makanan</label>
                    <input type="text" name="nama_makanan" value="{{ $meal->nama_makanan }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-0.5">Kalori</label>
                        <input type="number" name="kalori" value="{{ $meal->kalori }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-0.5">Protein (g)</label>
                        <input type="number" name="protein" value="{{ $meal->protein }}" min="0" step="0.1" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-0.5">Karbo (g)</label>
                        <input type="number" name="karbohidrat" value="{{ $meal->karbohidrat }}" min="0" step="0.1" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-0.5">Lemak (g)</label>
                        <input type="number" name="lemak" value="{{ $meal->lemak }}" min="0" step="0.1" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>
                </div>
                <input type="hidden" name="porsi" value="{{ $meal->porsi }}">
            </div>
            @endif

            <div class="flex items-center gap-3">
                <button type="submit" :disabled="!canSubmit" class="btn-success flex-1 disabled:opacity-40 disabled:cursor-not-allowed">{{ isset($meal) ? 'Perbarui' : 'Simpan Makanan' }}</button>
                <a href="{{ route('diet.meals.index') }}" class="rounded-lg bg-white px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 border border-gray-200">Batal</a>
            </div>
        </div>
    </form>
</div>

<script>
function mealCombo() {
    const allFoods = @json($foods ?? []);

    return {
        allFoods,
        items: [],
        search: '',
        filterKat: '',
        totalKalori: {{ $meal->kalori ?? 0 }},
        totalProtein: {{ $meal->protein ?? 0 }},
        totalKarbo: {{ $meal->karbohidrat ?? 0 }},
        totalLemak: {{ $meal->lemak ?? 0 }},
        namaGabungan: '{{ $meal->nama_makanan ?? "" }}',
        isEdit: {{ isset($meal) ? 'true' : 'false' }},

        get filteredFoods() {
            return this.allFoods.filter(f => {
                const matchSearch = !this.search || f.nama.toLowerCase().includes(this.search.toLowerCase());
                const matchKat = !this.filterKat || f.kategori === this.filterKat;
                return matchSearch && matchKat;
            });
        },

        get canSubmit() {
            return this.isEdit || this.items.length > 0;
        },

        isSelected(id) {
            return this.items.some(i => i.id === id);
        },

        addItem(food) {
            const existing = this.items.find(i => i.id === food.id);
            if (existing) {
                existing.porsi += 1;
            } else {
                this.items.push({
                    id: food.id,
                    nama: food.nama,
                    kalori: food.kalori,
                    protein: food.protein,
                    karbo: food.karbohidrat,
                    lemak: food.lemak,
                    porsi: 1,
                    satuan: food.satuan_porsi,
                    manual: false,
                });
            }
            this.recalc();
        },

        addManual() {
            const nama = this.$refs.manualNama.value.trim();
            const kalori = parseInt(this.$refs.manualKalori.value) || 0;
            const protein = parseFloat(this.$refs.manualProtein.value) || 0;
            if (!nama) { alert('Isi nama makanan'); return; }

            this.items.push({
                id: 'manual_' + Date.now(),
                nama: nama,
                kalori: kalori,
                protein: protein,
                karbo: 0,
                lemak: 0,
                porsi: 1,
                satuan: '1 porsi',
                manual: true,
            });
            this.$refs.manualNama.value = '';
            this.$refs.manualKalori.value = '';
            this.$refs.manualProtein.value = '';
            this.recalc();
        },

        r(v) { return Math.round(v * 10) / 10; },

        recalc() {
            this.totalKalori = 0;
            this.totalProtein = 0;
            this.totalKarbo = 0;
            this.totalLemak = 0;
            const names = [];

            this.items.forEach(i => {
                this.totalKalori += Math.round(i.kalori * i.porsi);
                this.totalProtein += this.r(i.protein * i.porsi);
                this.totalKarbo += this.r(i.karbo * i.porsi);
                this.totalLemak += this.r(i.lemak * i.porsi);
                names.push(i.porsi !== 1 ? i.nama + ' x' + i.porsi : i.nama);
            });

            this.totalProtein = this.r(this.totalProtein);
            this.totalKarbo = this.r(this.totalKarbo);
            this.totalLemak = this.r(this.totalLemak);
            this.namaGabungan = names.join(' + ');
        }
    }
}
</script>
@endsection
