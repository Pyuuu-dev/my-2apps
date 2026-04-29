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
            let text = '';

            if (this.sections.header) {
                text += this.headerText.trim() + '\n\n';
            }

            if (this.sections.fruit) {
                const items = this.showZeroStock ? this.fruits : this.fruits.filter(f => f.stok > 0);
                if (items.length > 0) {
                    text += `"🍎 FRUIT\n`;
                    items.forEach(f => {
                        text += `🔥 ${f.nama} → ${this.fmt(f.harga_jual)}${f.stok > 0 ? ' (' + f.stok + ')' : ''}\n`;
                    });
                    text += `\n`;
                }
            }

            if (this.sections.skin) {
                const items = this.showZeroStock ? this.skins : this.skins.filter(s => s.stok > 0);
                if (items.length > 0) {
                    text += `🎨 SKIN\n`;
                    items.forEach(s => {
                        text += `🔥 ${s.nama} → ${this.fmt(s.harga_jual)}${s.stok > 0 ? ' (' + s.stok + ')' : ''}\n`;
                    });
                    text += `\n`;
                }
            }

            if (this.sections.gamepass) {
                text += `🎮 GAMEPASS\n`;
                this.gamepasses.forEach(g => {
                    text += `🔥 ${g.nama} → ${this.fmt(g.harga_jual)}\n`;
                });
                text += `\n`;
            }

            if (this.sections.permanent) {
                const items = this.permanents.filter(p => p.harga_jual > 0);
                if (items.length > 0) {
                    text += `💎 PERMANEN\n`;
                    items.forEach(p => {
                        text += `🔥 Perm ${p.nama} → ${this.fmt(p.harga_jual)}\n`;
                    });
                }
            }

            return text.trimEnd() + `"`;
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
