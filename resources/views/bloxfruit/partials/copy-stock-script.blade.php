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
function stockPage(fruits, skins, gamepasses, permanents) {
    // Header default dibaca dari Settings (key: store.copy_header_template)
    // Fallback ke template default kalau setting belum diisi
    const defaultHeader = {!! json_encode($defaultHeader, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};

    return {
        showCopy: false,
        copied: false,
        showZeroStock: false,
        sections: { header: true, fruit: true, skin: true, gamepass: true, permanent: true },
        headerText: localStorage.getItem('ldc_copy_header') || defaultHeader,
        fruits: fruits || [],
        skins: skins || [],
        gamepasses: gamepasses || [],
        permanents: permanents || [],

        init() {
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

            if (this.sections.fruit) {
                const items = this.showZeroStock ? this.fruits : this.fruits.filter(f => f.stok > 0);
                if (items.length > 0) {
                    let s = '🍎 FRUIT';
                    items.forEach(f => { s += `\n🔥 ${f.nama} → ${this.fmt(f.harga_jual)}`; });
                    parts.push(s);
                }
            }

            if (this.sections.skin) {
                const items = this.showZeroStock ? this.skins : this.skins.filter(s => s.stok > 0);
                if (items.length > 0) {
                    let s = '🎨 SKIN';
                    items.forEach(sk => { s += `\n🔥 ${sk.nama} → ${this.fmt(sk.harga_jual)}`; });
                    parts.push(s);
                }
            }

            if (this.sections.gamepass) {
                const items = this.showZeroStock ? this.gamepasses : this.gamepasses.filter(g => g.stok > 0);
                if (items.length > 0) {
                    let s = '🎮 GAMEPASS';
                    items.forEach(g => { s += `\n🔥 ${g.nama} → ${this.fmt(g.harga_jual)}`; });
                    parts.push(s);
                }
            }

            if (this.sections.permanent) {
                // Permanent: hanya tampil yang stok > 0 dan harga_jual > 0
                const items = this.showZeroStock
                    ? this.permanents.filter(p => p.harga_jual > 0)
                    : this.permanents.filter(p => p.harga_jual > 0 && p.stok > 0);
                if (items.length > 0) {
                    let s = '💎 PERMANEN';
                    items.forEach(p => { s += `\n🔥 Perm ${p.nama} → ${this.fmt(p.harga_jual)}`; });
                    parts.push(s);
                }
            }

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
