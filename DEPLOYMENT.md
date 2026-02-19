# 🚀 Panduan Deployment Production - POS Sekar Langit

> Panduan lengkap untuk setup POS Sekar Langit di Laragon (Production Ready)

## 📋 Persyaratan

- **Laragon** (sudah terinstall)
- **PHP 8.2+** (ada di Laragon)
- **Composer** (ada di Laragon)
- **Node.js** (minimal v18)
- **NPM** (ikut dengan Node.js)

---

## 🔧 Setup Production (One-Time Setup)

### **Step 1: Clone/Copy Project**

Pastikan folder project ada di: `C:\laragon\www\pos-sekarlangit`

### **Step 2: Install Dependencies**

Buka terminal/command prompt di folder project, jalankan:

```bash
composer install --optimize-autoloader --no-dev
npm install
```

### **Step 3: Setup Environment**

1. Copy `.env.production.example` ke `.env`:

    ```bash
    copy .env.production.example .env
    ```

2. Generate application key:

    ```bash
    php artisan key:generate
    ```

3. Buka file `.env` dan pastikan konfigurasi sudah benar:
    ```
    APP_URL=http://pos-sekarlangit.test
    APP_ENV=production
    APP_DEBUG=false
    DB_CONNECTION=sqlite
    ```

### **Step 4: Setup Database**

```bash
# Buat file database
type nul > database\database.sqlite

# Jalankan migration
php artisan migrate --force

# (Optional) Seed data contoh
php artisan db:seed
```

### **Step 5: Build Production Assets**

**Cara Otomatis (RECOMMENDED):**

Double-click file: `build-production.bat`

**Atau Manual:**

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### **Step 6: Setup Virtual Host di Laragon**

1. Buka Laragon
2. Klik kanan icon Laragon → Menu → Auto Virtual Hosts → Enabled
3. Restart Laragon
4. Laragon otomatis buat virtual host: `http://pos-sekarlangit.test`

**Jika tidak otomatis:**

- Pastikan folder project di `C:\laragon\www\pos-sekarlangit`
- Restart Laragon dengan "Start All"

---

## ✅ Cara Menggunakan (Daily Use)

### **Untuk Kasir/Operator Toko:**

1. **Nyalakan Laragon**
    - Buka aplikasi Laragon
    - Klik "Start All"
    - Tunggu sampai Apache & MySQL running (icon hijau)

2. **Buka Aplikasi POS**
    - Buka browser (Chrome/Firefox)
    - Ketik: `http://pos-sekarlangit.test`
    - Tekan Enter

3. **Aplikasi Siap Digunakan!** 🎉

4. **Selesai Kerja:**
    - Close browser
    - (Optional) Stop Laragon: Klik "Stop All"

---

## 🔄 Update Aplikasi (Setelah Ada Perubahan Code)

Jika ada update dari developer:

```bash
# 1. Pull changes (jika pakai Git)
git pull

# 2. Update dependencies (jika ada perubahan)
composer install --optimize-autoloader --no-dev
npm ci

# 3. Run migration (jika ada perubahan database)
php artisan migrate --force

# 4. Rebuild assets
npm run build

# 5. Clear & cache ulang
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Atau jalankan:** `build-production.bat`

---

## 🛠️ Troubleshooting

### **Problem: Halaman blank/error 500**

**Solution:**

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### **Problem: Assets tidak muncul (CSS/JS hilang)**

**Solution:**

```bash
npm run build
php artisan optimize:clear
```

### **Problem: Database error**

**Solution:**

```bash
# Check file database.sqlite ada
dir database\database.sqlite

# Jika tidak ada, buat ulang
type nul > database\database.sqlite
php artisan migrate --force
```

### **Problem: Tidak bisa akses pos-sekarlangit.test**

**Solution:**

1. Pastikan Laragon running (Start All)
2. Restart Laragon
3. Check folder name: harus `pos-sekarlangit` (dengan hyphen)
4. Flush DNS: `ipconfig /flushdns`

### **Problem: Permission error di database**

**Solution:**

```bash
# Pastikan file database bisa ditulis
icacls database\database.sqlite /grant Everyone:F
```

---

## 📁 Struktur File Production

```
pos-sekarlangit/
├── database/
│   └── database.sqlite          # Database SQLite (JANGAN HAPUS!)
├── public/
│   └── build/                   # Assets compiled (hasil npm run build)
├── storage/
│   ├── app/                     # File upload
│   ├── logs/                    # Log aplikasi
│   └── framework/               # Cache Laravel
├── .env                         # Configuration (RAHASIA!)
├── build-production.bat         # Script build otomatis
└── composer.json / package.json # Dependencies
```

---

## 🔒 Keamanan Production

1. ✅ **APP_DEBUG=false** di `.env`
2. ✅ **APP_ENV=production** di `.env`
3. ✅ **Backup database** secara berkala:
    ```bash
    copy database\database.sqlite database\backup\database-2026-02-19.sqlite
    ```
4. ✅ **Jangan share file** `.env` (berisi APP_KEY!)

---

## 📊 Monitoring

### **Check Application Status:**

```bash
php artisan about
```

### **View Logs:**

```bash
# Lihat log error
type storage\logs\laravel.log
```

### **Database Backup:**

```bash
# Manual backup
copy database\database.sqlite "database\backup\db-%date:~-4,4%%date:~-10,2%%date:~-7,2%.sqlite"
```

---

## 🆘 Support

Jika ada masalah:

1. Check log: `storage/logs/laravel.log`
2. Check terminal error messages
3. Restart Laragon
4. Contact developer

---

## ✨ Tips Production

1. **Auto Start Laragon:**
    - Laragon → Preferences → General → Start Laragon when Windows boots

2. **Database Backup Otomatis:**
    - Buat scheduled task Windows untuk backup database setiap hari

3. **Performance:**
    - Pastikan selalu run `php artisan optimize` setelah update
    - Jangan enable `APP_DEBUG=true` di production

4. **Browser Bookmark:**
    - Bookmark: `http://pos-sekarlangit.test`
    - Set as homepage untuk kasir

---

**Version:** 1.0  
**Last Updated:** February 2026  
**Maintainer:** POS Sekar Langit Development Team
