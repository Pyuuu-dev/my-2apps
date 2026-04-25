#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script untuk memproses data inventori game dan membuat ringkasan
Cara pakai: python3 process_full_inventory.py input_data.txt
"""

import sys
from datetime import datetime
from collections import defaultdict

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
        if not line.strip():
            continue
            
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
    # Baca dari stdin jika tidak ada argument
    if len(sys.argv) > 1:
        with open(sys.argv[1], 'r', encoding='utf-8') as f:
            data_text = f.read()
    else:
        print("Membaca data dari stdin...")
        print("Paste data Anda, lalu tekan Ctrl+D (Linux/Mac) atau Ctrl+Z (Windows) untuk selesai:")
        data_text = sys.stdin.read()
    
    print("\nMemproses data...")
    
    # Parse data
    accounts, all_items = parse_data(data_text)
    
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
