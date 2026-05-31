@php
    $defaultHeader = setting('store.copy_header_template');
    if (empty($defaultHeader)) {
        $ig = setting('store.instagram_url', '');
        $tt = setting('store.tiktok_url', '');
        $wa = setting('store.wa_number', '');
        $defaultHeader = "📩 DM ON Instagram : {$ig}\n📩 DM ON Tiktok : {$tt}\n📩 Admin WhatsApp : https://wa.me/{$wa}?text=Min%20Saya%20ingin%20beli\n\n💳 Payment : GOPAY / DANA / Qris ALL PAYMENT FREE TAX";
    }
@endphp
<script>
function jokiPromoPage(jokiData) {
    // Header default dibaca dari Settings (key: store.copy_header_template)
    // Fallback ke template default kalau setting belum diisi
    // localStorage key 'ldc_copy_header' di-share dengan halaman stock biar sinkron
    const defaultHeader = {!! json_encode($defaultHeader, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};

    return {
        showCopy: false,
        copied: false,
        includeKeterangan: false,
        sections: { header: true },
        jokiData: jokiData || [],
        headerText: localStorage.getItem('ldc_copy_header') || defaultHeader,

        init() {
            // Generate checkbox state per kategori (default semua tercentang)
            this.jokiData.forEach(cat => {
                this.sections['kat_' + cat.kategori] = true;
            });
            this.$watch('headerText', (val) => localStorage.setItem('ldc_copy_header', val));
        },

        resetHeader() {
            localStorage.removeItem('ldc_copy_header');
            this.headerText = defaultHeader;
        },

        get generatedText() {
            let parts = [];

            if (this.sections.header) {
                parts.push(this.headerText.trim());
            }

            this.jokiData.forEach(cat => {
                if (!this.sections['kat_' + cat.kategori]) return;
                if (!cat.items || cat.items.length === 0) return;

                let s = `${cat.icon} ${cat.label.toUpperCase()}`;
                cat.items.forEach(it => {
                    let line = `\n🔥 ${it.nama} → ${this.fmt(it.harga)}`;
                    if (this.includeKeterangan && it.keterangan) {
                        line += ` (${it.keterangan})`;
                    }
                    s += line;
                });
                parts.push(s);
            });

            return parts.join('\n\n');
        },

        fmt(n) { return new Intl.NumberFormat('id-ID').format(n); },

        copyText() {
            navigator.clipboard.writeText(this.generatedText).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            });
        }
    }
}
</script>
