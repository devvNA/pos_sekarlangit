@echo off
REM ==========================================
REM POS Sekar Langit - Production Build Script
REM ==========================================

echo.
echo ========================================
echo  POS Sekar Langit - Production Build
echo ========================================
echo.

echo [1/6] Installing Composer dependencies...
call composer install --optimize-autoloader --no-dev
if errorlevel 1 goto error

echo.
echo [2/6] Installing NPM dependencies...
call npm ci
if errorlevel 1 goto error

echo.
echo [3/6] Building production assets...
call npm run build
if errorlevel 1 goto error

echo.
echo [4/6] Clearing all caches...
call php artisan cache:clear
call php artisan config:clear
call php artisan route:clear
call php artisan view:clear

echo.
echo [5/6] Optimizing for production...
call php artisan config:cache
call php artisan route:cache
call php artisan view:cache
call php artisan optimize

echo.
echo [6/6] Setting proper permissions...
if not exist "database\database.sqlite" (
    type nul > database\database.sqlite
    echo Created SQLite database file
)

echo.
echo ========================================
echo  BUILD SUCCESS!
echo ========================================
echo.
echo File assets ada di: public\build\
echo Database: database\database.sqlite
echo.
echo Akses aplikasi via Laragon:
echo http://pos-sekarlangit.test
echo.
pause
goto end

:error
echo.
echo ========================================
echo  BUILD FAILED!
echo ========================================
echo.
echo Silakan cek error di atas.
pause
exit /b 1

:end
