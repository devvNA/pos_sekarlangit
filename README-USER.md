# 📖 Panduan Cepat - POS Sekar Langit

> Aplikasi Point of Sale untuk Toko Sekar Langit, Karanggintung

---

## 🚀 Instalasi Pertama Kali

### **Cara Super Mudah (Windows):**

1. **Pastikan Laragon sudah terinstall**

2. **Extract/Copy project ke:**  
   `C:\laragon\www\pos-sekarlangit`

3. **Double-click file:**  
   `setup-first-time.bat`

4. **Tunggu sampai selesai** (5-10 menit)

5. **Buka Laragon → Start All**

6. **Buka browser → Ketik:**  
   `http://pos-sekarlangit.test`

7. **✅ SELESAI!**

---

## 💻 Cara Pakai Sehari-hari

### **Untuk Kasir/Operator:**

1. Nyalakan **Laragon** (Start All)
2. Buka **browser** (Chrome/Firefox)
3. Ketik: `http://pos-sekarlangit.test`
4. Mulai transaksi!

**Selesai kerja?** Tutup browser saja. Laragon bisa dibiarkan running atau di-stop.

---

## 📂 File Penting (JANGAN DIHAPUS!)

| File                       | Fungsi                          |
| -------------------------- | ------------------------------- |
| `database/database.sqlite` | Database semua transaksi        |
| `.env`                     | Konfigurasi aplikasi (RAHASIA!) |
| `public/build/`            | File CSS/JS compiled            |
| `storage/`                 | File upload & logs              |

---

## 🔄 Update Aplikasi

Jika ada update dari developer:

**Double-click:** `build-production.bat`

---

## 🆘 Troubleshooting Cepat

### **Halaman Error/Blank?**

```
1. Buka folder project
2. Double-click: build-production.bat
3. Restart Laragon
```

### **CSS/JS Tidak Muncul?**

```
1. Jalankan: build-production.bat
2. Refresh browser (Ctrl+F5)
```

### **Database Error?**

```
1. Pastikan file database/database.sqlite ada
2. Jika tidak ada, jalankan: setup-first-time.bat
```

---

## 📸 Fitur Aplikasi

✅ **POS Kasir** - Scan barcode, checkout cepat  
✅ **Inventory** - Kelola produk & stok  
✅ **Piutang** - Catat hutang pelanggan  
✅ **Buku Kas** - Pencatatan keuangan  
✅ **Laporan** - Chart & export CSV  
✅ **Supplier** - Kelola pemasok

---

## 📞 Support

**Dokumentasi Lengkap:** Baca file `DEPLOYMENT.md`

**Butuh Bantuan?** Hubungi developer/IT support

---

## ✨ Tips

- **Backup Database:** Copy file `database/database.sqlite` secara berkala
- **Bookmark:** Simpan `http://pos-sekarlangit.test` di bookmark browser
- **Keyboard Shortcuts:** Gunakan Tab untuk navigasi form lebih cepat
- **Barcode Scanner:** Pastikan scanner dalam mode "keyboard emulation"

---

**Version:** 1.0  
**Made with ❤️ for Toko Sekar Langit**
