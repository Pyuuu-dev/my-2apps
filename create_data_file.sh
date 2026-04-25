#!/bin/bash
# Script untuk membuat file data_input.txt
# Paste semua data Anda setelah menjalankan script ini

echo "=========================================="
echo "MEMBUAT FILE DATA INPUT"
echo "=========================================="
echo ""
echo "Silakan paste SEMUA data Anda di bawah ini"
echo "Setelah selesai paste, tekan Ctrl+D"
echo ""
echo "Menunggu input..."
echo ""

cat > /var/www/app/data_input.txt

echo ""
echo "=========================================="
echo "File data_input.txt berhasil dibuat!"
echo "=========================================="
echo ""
echo "Jumlah baris: $(wc -l < /var/www/app/data_input.txt)"
echo ""
echo "Sekarang jalankan:"
echo "  python3 /var/www/app/process_full_inventory.py /var/www/app/data_input.txt"
echo ""
