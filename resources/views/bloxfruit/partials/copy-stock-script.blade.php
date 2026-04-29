<script>
function stockPage(fruits, skins, gamepasses, permanents) {
    const defaultHeader = `📩 DM ON Instagram : https://www.instagram.com/ldcstoree/
📩 DM ON Tiktok : https://www.tiktok.com/@ldc_storee
📩 Admin WhatsApp : https://wa.me/6282353085502?text=Min%20Saya%20ingin%20beli

⭐ Testi/Vouch ? Cek dibawah 1690+
    - Chuni Server *GA https://discord.gg/YAj7Dzhbw4
    - Google Drive https://shorturl.at/dwEeB
💳 Payment : GOPAY / DANA / Qris ALL PAYMENT FREE TAX`;

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

            if (this.sections.gamepass && this.gamepasses.length > 0) {
                let s = '🎮 GAMEPASS';
                this.gamepasses.forEach(g => { s += `\n🔥 ${g.nama} → ${this.fmt(g.harga_jual)}`; });
                parts.push(s);
            }

            if (this.sections.permanent) {
                const items = this.permanents.filter(p => p.harga_jual > 0);
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
