{{--
    Copy Stock Text Modal (reusable)
    Include this inside an Alpine x-data component that has:
    - showCopy (boolean)
    - fruits, skins, gamepasses, permanents (arrays from controller)
--}}
<div x-show="showCopy" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="showCopy = false">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showCopy = false"></div>
    <div class="relative w-full max-w-2xl max-h-[85vh] rounded-2xl bg-white dark:bg-slate-900 shadow-2xl overflow-hidden flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-slate-700 shrink-0">
            <h3 class="font-bold text-gray-900 dark:text-white">Copy Teks Stok</h3>
            <button @click="showCopy = false" class="rounded-lg p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-slate-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Options --}}
        <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 shrink-0 space-y-3">
            <div class="flex flex-wrap gap-2">
                <label class="flex items-center gap-1.5 text-xs">
                    <input type="checkbox" x-model="sections.header" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Header
                </label>
                <label class="flex items-center gap-1.5 text-xs">
                    <input type="checkbox" x-model="sections.fruit" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Fruit
                </label>
                <label class="flex items-center gap-1.5 text-xs">
                    <input type="checkbox" x-model="sections.skin" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Skin
                </label>
                <label class="flex items-center gap-1.5 text-xs">
                    <input type="checkbox" x-model="sections.gamepass" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Gamepass
                </label>
                <label class="flex items-center gap-1.5 text-xs">
                    <input type="checkbox" x-model="sections.permanent" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Permanent
                </label>
                <label class="flex items-center gap-1.5 text-xs">
                    <input type="checkbox" x-model="showZeroStock" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Tampilkan stok 0
                </label>
            </div>
            {{-- Editable header --}}
            <div x-show="sections.header" x-collapse>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">Edit Header:</label>
                <textarea x-model="headerText" rows="6" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-gray-200 text-xs font-mono focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
        </div>

        {{-- Preview --}}
        <div class="flex-1 overflow-y-auto px-5 py-3">
            <pre class="text-xs text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-mono leading-relaxed" x-text="generatedText"></pre>
        </div>

        {{-- Footer --}}
        <div class="px-5 py-3 border-t border-gray-100 dark:border-slate-700 shrink-0 flex items-center gap-3">
            <button @click="copyText()" class="flex-1 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700 transition-colors">
                <span x-show="!copied">Copy ke Clipboard</span>
                <span x-show="copied" x-cloak>Tersalin!</span>
            </button>
            <button @click="showCopy = false" class="rounded-lg bg-gray-100 dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300">Tutup</button>
        </div>
    </div>
</div>
