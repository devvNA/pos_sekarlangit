@echo off
REM ==========================================
REM POS Sekar Langit - Quick Start Setup
REM Untuk pertama kali install saja
REM ==========================================

echo.
echo ========================================
echo  POS Sekar Langit - First Time Setup
echo ========================================
echo.

REM Check if .env exists
if exist ".env" (
    echo File .env sudah ada. Setup sudah pernah dilakukan.
    echo Jika ingin setup ulang, hapus dulu file .env
    echo.
    pause
    exit /b 0
)

echo [1/8] Checking requirements...
where composer >nul 2>nul
if errorlevel 1 (
    echo ERROR: Composer tidak ditemukan!
    echo Pastikan Laragon sudah terinstall dan ada di PATH
    pause
    exit /b 1
)

where npm >nul 2>nul
if errorlevel 1 (
    echo ERROR: NPM tidak ditemukan!
    echo Install Node.js terlebih dahulu
    pause
    exit /b 1
)

echo Composer... OK
echo NPM... OK
echo.

echo [2/8] Installing Composer dependencies...
call composer install --optimize-autoloader --no-dev
if errorlevel 1 goto error

echo.
echo [3/8] Installing NPM dependencies...
call npm install
if errorlevel 1 goto error

echo.
echo [4/8] Setting up environment...
if not exist ".env" (
    copy .env.production.example .env
    echo File .env created
)

echo.
echo [5/8] Generating application key...
call php artisan key:generate --force
if errorlevel 1 goto error

echo.
echo [6/8] Setting up database...
if not exist "database\database.sqlite" (
    type nul > database\database.sqlite
    echo SQLite database created
)

echo.
echo Running migrations...
call php artisan migrate --force
if errorlevel 1 goto error

echo.
echo Seeding demo data... (optional, tekan Ctrl+C untuk skip)
call php artisan db:seed
REM Ignore error jika user skip seeding

echo.
echo [7/8] Building production assets...
call npm run build
if errorlevel 1 goto error

echo.
echo [8/8] Optimizing application...
call php artisan config:cache
call php artisan route:cache
call php artisan view:cache
call php artisan optimize

echo.
echo ========================================
echo  SETUP SELESAI!
echo ========================================
echo.
echo Langkah selanjutnya:
echo 1. Buka Laragon ^-^> Klik "Start All"
echo 2. Buka browser ^-^> Ketik: http://pos-sekarlangit.test
echo 3. Aplikasi siap digunakan!
echo.
echo File penting:
echo - Database: database\database.sqlite
echo - Config: .env
echo - Assets: public\build\
echo.
echo Untuk update nanti, jalankan: build-production.bat
echo.
pause
goto end

:error
echo.
echo ========================================
echo  SETUP GAGAL!
echo ========================================
echo.
echo Silakan cek error di atas dan coba lagi.
echo.
pause
exit /b 1

:end
