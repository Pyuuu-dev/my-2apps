#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script untuk memproses data inventori game dan membuat ringkasan
"""

from datetime import datetime
from collections import defaultdict

# Data mentah dari user (akan diproses untuk menghapus duplikasi)
raw_data = """CHROME 7	pyuuuu_p	Dragon West	1
CHROME 7	pyuuuu_p	Dragon East	3
CHROME 7	pyuuuu_p	Kitsune	
CHROME 7	pyuuuu_p	Control	3
CHROME 7	pyuuuu_p	Yeti	
CHROME 7	pyuuuu_p	Tiger	
CHROME 7	pyuuuu_p	Spirit	1
CHROME 7	pyuuuu_p	Gas	1
CHROME 7	pyuuuu_p	Venom	3
CHROME 7	pyuuuu_p	Shadow	6
CHROME 7	pyuuuu_p	Dough	3
CHROME 7	pyuuuu_p	T-Rex	
CHROME 7	pyuuuu_p	Mammoth	4
CHROME 7	pyuuuu_p	Gravity	2
CHROME 7	pyuuuu_p	Pain	2
CHROME 7	pyuuuu_p	Lightning	
CHROME 7	pyuuuu_p	Portal	2
CHROME 7	pyuuuu_p	Buddha	3
CHROME 7	pyuuuu_p	Dragon Ember West	1
CHROME 7	pyuuuu_p	Galaxy Kitsune	
CHROME 7	pyuuuu_p	Empyrean Kitsune	
CHROME 7	pyuuuu_p	Fiend Yeti	3
CHROME 7	pyuuuu_p	Werewolf	1
CHROME 7	pyuuuu_p	Divine Portal	
CHROME 7	pyuuuu_p	Purple Lightning	
CHROME 7	pyuuuu_p	Green Lightning	
CHROME 7	pyuuuu_p	Torment Pain	5
CHROME 7	pyuuuu_p	Topaz Diamond	
CHROME 7	pyuuuu_p	Emerald Diamond	
CHROME 7	pyuuuu_p	Ruby Diamond	
CHROME 7	pyuuuu_p	Glacier Eagle	1
CHROME 7	pyuuuu_p	Celebration Bomb	2
CHROME 7	pyuuuu_p	2x Mastery	2
CHROME 7	pyuuuu_p	2x Money	3
CHROME 7	pyuuuu_p	1+ Storage	
CHROME 7	pyuuuu_p	2x Drop	4
CHROME 7	pyuuuu_p	Fast Boat	2
CHROME 7	pyuuuu_p	Darkblade	2
CHROME 7	pyuuuu_p	Fruit Notifier	1
CHROME 7	pyuuuu_p	Perm Dragon	4
CHROME 7	pyuuuu_p	Perm Kitsune	1
CHROME 7	pyuuuu_p	Perm Control	1
CHROME 7	pyuuuu_p	Perm Yeti	
CHROME 7	pyuuuu_p	Perm Tiger	1
CHROME 7	pyuuuu_p	Perm Spirit	
CHROME 7	pyuuuu_p	Perm Gas	1
CHROME 7	pyuuuu_p	Perm Venom	
CHROME 7	pyuuuu_p	Perm Shadow	
CHROME 7	pyuuuu_p	Perm Dough	1
CHROME 7	pyuuuu_p	Perm T-Rex	1
CHROME 7	pyuuuu_p	Perm Mammoth	1
CHROME 7	pyuuuu_p	Perm Gravity	1
CHROME 7	pyuuuu_p	Perm Blizzard	
CHROME 7	pyuuuu_p	Perm Pain	1
CHROME 7	pyuuuu_p	Perm Lightning	1
CHROME 7	pyuuuu_p	Perm Portal	2
CHROME 7	pyuuuu_p	Perm Phonix	
CHROME 7	pyuuuu_p	Perm Sound	
CHROME 7	pyuuuu_p	Perm Spider	
CHROME 7	pyuuuu_p	Perm Creation	
CHROME 7	pyuuuu_p	Perm Love	
CHROME 7	pyuuuu_p	Perm Buddha	1
CHROME 7	pyuuuu_p	Perm Quake	
CHROME 7	pyuuuu_p	Perm Magma	1
CHROME 7	pyuuuu_p	Perm Ghost	
CHROME 7	pyuuuu_p	Perm Rubber	1
CHROME 7	pyuuuu_p	Perm Light	2
CHROME 7	pyuuuu_p	Perm Diamond	
CHROME 7	pyuuuu_p	Perm Eagle	
CHROME 7	pyuuuu_p	Perm Dark	
CHROME 7	pyuuuu_p	Perm Sand	
CHROME 7	pyuuuu_p	Perm Ice	2
CHROME 7	pyuuuu_p	Perm Flame	1
CHROME 7	pyuuuu_p	Perm Spike	
CHROME 7	pyuuuu_p	Perm Smoke	
CHROME 7	pyuuuu_p	Perm Bomb	
CHROME 7	pyuuuu_p	Perm Spring	
CHROME 7	pyuuuu_p	Perm Blade	
CHROME 7	pyuuuu_p	Perm Spin	1
CHROME 7	pyuuuu_p	Perm Rocket	"""

# Definisi kategori items
REGULAR_FRUITS = [
    "Dragon West", "Dragon East", "Kitsune", "Control", "Yeti", "Tiger", 
    "Spirit", "Gas", "Venom", "Shadow", "Dough", "T-Rex", "Mammoth", 
    "Gravity", "Pain", "Lightning", "Portal", "Buddha"
]

SKIN_VARIANTS = [
    "Dragon Ember West", "Galaxy Kitsune", "Empyrean Kitsune", "Fiend Yeti", 
    "Werewolf", "Divine Portal", "Purple Lightning", "Green Lightning", 
    "Torment Pain", "Topaz Diamond", "Emerald Diamond", "Ruby Diamond", 
    "Glacier Eagle", "Celebration Bomb"
]

GAMEPASS = [
    "2x Mastery", "2x Money", "1+ Storage", "2x Drop", "Fast Boat", 
    "Darkblade", "Fruit Notifier"
]

def categorize_item(item_name):
    """Kategorikan item berdasarkan namanya"""
    if item_name.startswith("Perm "):
        return "permanent"
    elif item_name in REGULAR_FRUITS:
        return "fruits"
    elif item_name in SKIN_VARIANTS:
        return "skins"
    elif item_name in GAMEPASS:
        return "gamepass"
    else:
        # Fallback logic
        if any(x in item_name for x in ["Lightning", "Diamond", "Eagle", "Bomb", "Ember", "Galaxy", "Empyrean", "Fiend", "Werewolf", "Divine", "Torment", "Glacier", "Celebration"]):
            return "skins"
        return "fruits"

def parse_data(data_text):
    """Parse data mentah menjadi struktur dictionary"""
    accounts = defaultdict(lambda: defaultdict(list))
    all_items = set()
    
    lines = data_text.strip().split('\n')
    seen = set()  # Untuk deduplikasi
    
    for line in lines:
        parts = line.split('\t')
        if len(parts) < 3:
            continue
            
        browser = parts[0].strip()
        username = parts[1].strip()
        item = parts[2].strip()
        qty = parts[3].strip() if len(parts) > 3 else ""
        
        # Skip jika kosong atau sudah pernah diproses (deduplikasi)
        key = f"{browser}|{username}|{item}"
        if key in seen:
            continue
        seen.add(key)
        
        # Parse quantity
        try:
            quantity = int(qty) if qty else 0
        except:
            quantity = 0
        
        # Skip jika quantity 0
        if quantity == 0:
            continue
        
        # Kategorikan item
        category = categorize_item(item)
        
        # Simpan ke struktur data
        account_key = f"{browser}|{username}"
        accounts[account_key][category].append((item, quantity))
        all_items.add(item)
    
    return accounts, all_items

def generate_account_summary(accounts):
    """Generate file ringkasan per akun"""
    output = []
    output.append("=" * 80)
    output.append("RINGKASAN INVENTORI PER AKUN")
    output.append(f"Tanggal: {datetime.now().strftime('%d %B %Y, %H:%M:%S')}")
    output.append(f"Total Akun: {len(accounts)}")
    output.append("=" * 80)
    output.append("")
    
    # Sort accounts by browser then username
    sorted_accounts = sorted(accounts.items(), key=lambda x: x[0])
    
    for idx, (account_key, categories) in enumerate(sorted_accounts, 1):
        browser, username = account_key.split('|')
        
        output.append(f"[{idx}] {browser} - {username}")
        output.append("─" * 80)
        
        # Fruits (Regular)
        if 'fruits' in categories and categories['fruits']:
            output.append("    📦 FRUITS (REGULAR):")
            for item, qty in sorted(categories['fruits']):
                output.append(f"       • {item} x{qty}")
            output.append("")
        
        # Fruits (Skin Variants)
        if 'skins' in categories and categories['skins']:
            output.append("    ✨ FRUITS (SKIN VARIANTS):")
            for item, qty in sorted(categories['skins']):
                output.append(f"       • {item} x{qty}")
            output.append("")
        
        # Gamepass
        if 'gamepass' in categories and categories['gamepass']:
            output.append("    🎮 GAMEPASS:")
            for item, qty in sorted(categories['gamepass']):
                output.append(f"       • {item} x{qty}")
            output.append("")
        
        # Permanent Fruits
        if 'permanent' in categories and categories['permanent']:
            output.append("    🔒 PERMANENT FRUITS:")
            for item, qty in sorted(categories['permanent']):
                output.append(f"       • {item} x{qty}")
            output.append("")
        
        output.append("")
    
    return '\n'.join(output)

def generate_items_list(all_items):
    """Generate file daftar lengkap items"""
    output = []
    output.append("=" * 80)
    output.append("DAFTAR LENGKAP SEMUA ITEM")
    output.append(f"Tanggal: {datetime.now().strftime('%d %B %Y, %H:%M:%S')}")
    output.append(f"Total Items: {len(all_items)}")
    output.append("=" * 80)
    output.append("")
    
    # Kategorikan semua items
    categorized = {
        'fruits': [],
        'skins': [],
        'gamepass': [],
        'permanent': []
    }
    
    for item in all_items:
        cat = categorize_item(item)
        categorized[cat].append(item)
    
    # Sort each category
    for cat in categorized:
        categorized[cat].sort()
    
    # Fruits (Regular)
    output.append(f"📦 KATEGORI 1: FRUITS (REGULAR) - {len(categorized['fruits'])} items")
    output.append("─" * 80)
    for idx, item in enumerate(categorized['fruits'], 1):
        output.append(f"{idx:2d}. {item}")
    output.append("")
    
    # Fruits (Skin Variants)
    output.append(f"✨ KATEGORI 2: FRUITS (SKIN VARIANTS) - {len(categorized['skins'])} items")
    output.append("─" * 80)
    for idx, item in enumerate(categorized['skins'], 1):
        output.append(f"{idx:2d}. {item}")
    output.append("")
    
    # Gamepass
    output.append(f"🎮 KATEGORI 3: GAMEPASS - {len(categorized['gamepass'])} items")
    output.append("─" * 80)
    for idx, item in enumerate(categorized['gamepass'], 1):
        output.append(f"{idx}. {item}")
    output.append("")
    
    # Permanent Fruits
    output.append(f"🔒 KATEGORI 4: PERMANENT FRUITS - {len(categorized['permanent'])} items")
    output.append("─" * 80)
    for idx, item in enumerate(categorized['permanent'], 1):
        output.append(f"{idx:2d}. {item}")
    output.append("")
    
    return '\n'.join(output)

def main():
    print("Memproses data...")
    
    # Parse data
    accounts, all_items = parse_data(raw_data)
    
    print(f"✓ Ditemukan {len(accounts)} akun unik")
    print(f"✓ Ditemukan {len(all_items)} item unik")
    
    # Generate ringkasan per akun
    print("\nMembuat ringkasan_per_akun.txt...")
    account_summary = generate_account_summary(accounts)
    with open('/var/www/app/ringkasan_per_akun.txt', 'w', encoding='utf-8') as f:
        f.write(account_summary)
    print("✓ File ringkasan_per_akun.txt berhasil dibuat")
    
    # Generate daftar lengkap items
    print("\nMembuat daftar_lengkap_items.txt...")
    items_list = generate_items_list(all_items)
    with open('/var/www/app/daftar_lengkap_items.txt', 'w', encoding='utf-8') as f:
        f.write(items_list)
    print("✓ File daftar_lengkap_items.txt berhasil dibuat")
    
    print("\n" + "=" * 80)
    print("SELESAI! Kedua file telah berhasil dibuat:")
    print("  1. /var/www/app/ringkasan_per_akun.txt")
    print("  2. /var/www/app/daftar_lengkap_items.txt")
    print("=" * 80)

if __name__ == "__main__":
    main()
