# AGENTS.md - POS Sekar Langit

> Dokumen panduan untuk AI agents yang bekerja pada project POS (Point of Sale) Sekar Langit.

## Project Overview

**POS Sekar Langit** adalah aplikasi Point of Sale berbasis web untuk manajemen penjualan retail. Aplikasi ini dibangun dengan Laravel 12 dan menggunakan SQLite sebagai database default.

### Tech Stack

| Komponen     | Versi                          |
| ------------ | ------------------------------ |
| PHP          | ^8.3                           |
| Laravel      | ^12.0                          |
| Tailwind CSS | ^4.0                           |
| Vite         | ^7.0                           |
| Database     | SQLite (default)               |
| Package JS   | html5-qrcode (barcode scanner) |

## Struktur Project

```
pos-sekarlangit/
├── app/
│   ├── Http/Controllers/     # Controller utama aplikasi
│   │   ├── CashBookController.php    # Manajemen kas harian
│   │   ├── DashboardController.php     # Dashboard utama
│   │   ├── InventoryController.php     # Manajemen stok/produk
│   │   ├── PosController.php           # Transaksi POS
│   │   ├── ReceivableController.php    # Manajemen piutang
│   │   ├── ReportController.php        # Laporan penjualan
│   │   └── SupplierController.php      # Manajemen pemasok
│   └── Models/               # Eloquent Models
│       ├── CashBookEntry.php
│       ├── Customer.php
│       ├── Product.php
│       ├── Receivable.php
│       ├── ReceivablePayment.php
│       ├── Sale.php
│       ├── SaleItem.php
│       ├── StockIn.php
│       ├── StockInItem.php
│       ├── Supplier.php
│       └── User.php
├── database/migrations/      # Semua migration tersedia
├── resources/views/          # Blade templates
│   ├── layouts/app.blade.php # Layout utama
│   ├── pos/                  # Halaman POS & struk
│   ├── inventory/            # Manajemen produk
│   ├── cash/                 # Buku kas
│   ├── receivables/          # Piutang
│   ├── reports/              # Laporan
│   └── suppliers/            # Pemasok
├── routes/web.php            # Route definitions
└── docs/                     # Dokumentasi tambahan
```

## Domain Model

### Core Entities

1. **Product** - Produk yang dijual
    - `supplier_id`, `name`, `barcode`, `unit`
    - `price_buy` (harga beli), `price_sell` (harga jual)
    - `stock`, `min_stock` (stok minimum), `active`

2. **Sale** - Transaksi penjualan
    - `customer_id`, `receipt_no` (nomor struk), `sold_at`
    - `payment_method`, `total`, `paid`, `change`, `note`

3. **SaleItem** - Item dalam transaksi
    - Relasi ke `Sale` dan `Product`
    - `quantity`, `price_buy`, `price_sell`

4. **Supplier** - Pemasok barang
    - `name`, `phone`, `address`

5. **StockIn** / **StockInItem** - Pencatatan barang masuk

6. **Receivable** / **ReceivablePayment** - Piutang & pembayaran

7. **CashBookEntry** - Pencatatan kas masuk/keluar

## Routes

| URL                         | Method | Controller                  | Name              | Keterangan               |
| --------------------------- | ------ | --------------------------- | ----------------- | ------------------------ |
| `/`                         | GET    | DashboardController@index   | dashboard         | Halaman utama            |
| `/pos`                      | GET    | PosController@index         | pos.index         | Halaman POS              |
| `/pos/items`                | POST   | PosController@addItem       | pos.items.add     | Tambah item ke keranjang |
| `/pos/items/{id}`           | DELETE | PosController@removeItem    | pos.items.remove  | Hapus item               |
| `/pos/clear`                | DELETE | PosController@clearCart     | pos.clear         | Kosongkan keranjang      |
| `/pos/checkout`             | POST   | PosController@checkout      | pos.checkout      | Proses pembayaran        |
| `/pos/receipt/{sale}`       | GET    | PosController@receipt       | pos.receipt       | Cetak struk              |
| `/inventori`                | GET    | InventoryController@index   | inventory.index   | Daftar produk            |
| `/inventori/tambah`         | GET    | InventoryController@create  | inventory.create  | Form tambah produk       |
| `/inventori`                | POST   | InventoryController@store   | inventory.store   | Simpan produk            |
| `/inventori/{product}/edit` | GET    | InventoryController@edit    | inventory.edit    | Form edit                |
| `/inventori/{product}`      | PUT    | InventoryController@update  | inventory.update  | Update produk            |
| `/inventori/{product}`      | DELETE | InventoryController@destroy | inventory.destroy | Hapus produk             |
| `/pemasok`                  | GET    | SupplierController@index    | suppliers.index   | Daftar pemasok           |
| `/piutang`                  | GET    | ReceivableController@index  | receivables.index | Daftar piutang           |
| `/kas`                      | GET    | CashBookController@index    | cash.index        | Buku kas                 |
| `/laporan`                  | GET    | ReportController@index      | reports.index     | Laporan                  |
| `/laporan/export`           | GET    | ReportController@export     | reports.export    | Export laporan           |

## Coding Standards

### PHP / Laravel

- **Naming**: Gunakan `camelCase` untuk methods/variables, `PascalCase` untuk classes, `snake_case` untuk database columns
- **Return Types**: Selalu deklarasikan return type pada methods
- **Type Hints**: Gunakan type hints untuk parameters dan properties
- **Fillable**: Definisikan `$fillable` atau `$guarded` di semua models
- **Casts**: Gunakan `$casts` untuk tipe data decimal, datetime, boolean
- **Relationships**: Eager load relationships untuk menghindari N+1
- **Validation**: Validasi selalu di controller atau FormRequest

### Controllers

```php
// Gunakan method injection
public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'price_sell' => 'required|numeric|min:0',
    ]);

    Product::create($validated);

    return redirect()->route('inventory.index')
        ->with('success', 'Produk berhasil ditambahkan');
}
```

### Models

```php
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'name',
        'barcode',
        'unit',
        'price_buy',
        'price_sell',
        'stock',
        'min_stock',
        'active',
    ];

    protected $casts = [
        'price_buy' => 'decimal:2',
        'price_sell' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
```

### Blade Views

- Gunakan `x-app-layout` untuk layout utama
- Gunakan `@csrf` di semua form
- Method PUT/DELETE menggunakan `@method('PUT')`
- Gunakan `old('field')` untuk mempertahankan input setelah validasi gagal

### CSS (Tailwind)

- Tailwind v4 sudah dikonfigurasi dengan Vite
- Gunakan utility classes, hindari custom CSS jika memungkinkan
- Warna utama: sesuaikan dengan tema POS Sekar Langit

## Commands & Scripts

```bash
# Setup project
composer run setup

# Development (menjalankan server, queue, logs, dan vite)
composer run dev

# Run tests
composer run test

# Format code dengan Laravel Pint
./vendor/bin/pint

# Manual commands
php artisan serve          # Development server
npm run dev                # Vite dev server
npm run build              # Build assets
php artisan migrate        # Run migrations
php artisan db:seed        # Run seeders
```

## Development Workflow

1. **Migration**: Buat migration baru jika ada perubahan schema
2. **Model**: Update model dengan fillable dan casts yang sesuai
3. **Controller**: Implementasikan logic bisnis
4. **View**: Buat/update blade template
5. **Route**: Daftarkan route di `routes/web.php`
6. **Test**: Jalankan `composer run test`

## Fitur Kunci yang Perlu Diperhatikan

### 1. POS (Point of Sale)

- Barcode scanner menggunakan `html5-qrcode`
- Cart/session management
- Perhitungan otomatis: total, kembalian
- Cetak struk (`receipt.blade.php`)

### 2. Inventory Management

- Stok minimum warning
- Pencatatan barang masuk (StockIn)
- Harga beli vs harga jual

### 3. Piutang (Receivables)

- Pencatatan pembayaran kredit
- History pembayaran per customer

### 4. Buku Kas (Cash Ledger)

- Pencatatan pemasukan/pengeluaran
- Balance harian

### 5. Laporan

- Export data (Excel/PDF)
- Filter per periode

## Database

- Default menggunakan **SQLite** (`DB_CONNECTION=sqlite`)
- File database: `database/database.sqlite`
- Jalankan `php artisan migrate` untuk membuat tabel

## Components

### Confirm Modal (`<x-confirm-modal>`)

Komponen modal konfirmasi reusable untuk konfirmasi delete dan aksi lainnya.

**Props:**
| Prop | Default | Keterangan |
|------|---------|------------|
| `id` | `confirm-modal` | ID unik untuk modal |
| `title` | `Konfirmasi` | Judul modal |
| `message` | `Apakah Anda yakin?` | Pesan konfirmasi |
| `confirmText` | `Ya, Lanjutkan` | Text tombol confirm |
| `cancelText` | `Batal` | Text tombol batal |
| `confirmType` | `danger` | Warna tombol: `danger`, `warning`, `primary`, `success` |
| `icon` | `warning` | Icon: `warning`, `danger`, `info`, `question`, `success` |

**Cara Penggunaan:**

```blade
{{-- Definisikan modal --}}
<x-confirm-modal
    id="delete-modal"
    title="Hapus Produk"
    message="Apakah Anda yakin ingin menghapus produk ini?"
    confirm-text="Ya, Hapus"
    cancel-text="Batal"
    confirm-type="danger"
    icon="warning"
/>

{{-- Panggil via JavaScript --}}
<button onclick="confirmDelete(1, 'Produk ABC')">Hapus</button>

<script>
function confirmDelete(id, name) {
    deleteModalShow({
        message: `Hapus "${name}"?`,
        formAction: `/inventori/${id}`,
        formMethod: 'DELETE'
    });
}
</script>
```

**JavaScript Options:**

- `message` - Pesan custom (bisa HTML)
- `formAction` - URL form submit
- `formMethod` - HTTP method (POST, DELETE, PUT)
- `onConfirm()` - Callback function saat confirm
- `onCancel()` - Callback function saat cancel

**Note:** ID dengan hyphen (misal `delete-modal`) akan dikonversi ke camelCase untuk nama fungsi (`deleteModalShow`).

## Catatan Penting

1. **Barcode Scanner**: Gunakan library `html5-qrcode` yang sudah terinstall
2. **Session Cart**: POS menggunakan session untuk menyimpan keranjang sementara
3. **Receipt Number**: Generate nomor struk unik per transaksi
4. **Stock Management**: Stok berkurang saat checkout, bertambah saat StockIn
5. **Decimal**: Selalu gunakan `decimal:2` untuk field harga/uang

## Troubleshooting

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Fresh database
php artisan migrate:fresh --seed

# Permission issues (Linux/Mac)
chmod -R 775 storage bootstrap/cache
```

## Deployment & Production

### Production Setup (Laragon)

**Quick Start:**

```bash
# First time setup
setup-first-time.bat

# Build production
build-production.bat

# Or via composer
composer run production
```

**Manual Steps:**

```bash
# 1. Install dependencies (production)
composer install --optimize-autoloader --no-dev
npm ci

# 2. Setup environment
copy .env.production.example .env
php artisan key:generate

# 3. Setup database
type nul > database\database.sqlite
php artisan migrate --force

# 4. Build assets
npm run build

# 5. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Composer Scripts

```bash
composer run setup       # First-time setup (development)
composer run dev         # Development server + watch
composer run build       # Build assets + optimize
composer run production  # Full production build
composer run test        # Run tests
```

### Laragon Virtual Host

**Auto Configuration:**

- Folder: `C:\laragon\www\pos-sekarlangit`
- URL: `http://pos-sekarlangit.test`
- Laragon auto-detects and creates virtual host

**Manual Host (if needed):**

1. Laragon → Menu → Auto Virtual Hosts → Enabled
2. Restart Laragon
3. Access via browser: `http://pos-sekarlangit.test`

### Daily Usage (End User)

1. Start Laragon → "Start All"
2. Open browser → `http://pos-sekarlangit.test`
3. Application ready!

**No need:**

- ❌ `npm run dev`
- ❌ `php artisan serve`
- ❌ Terminal commands

### File Structure Production

```
pos-sekarlangit/
├── .env                        # Config (SECRET!)
├── database/
│   └── database.sqlite         # SQLite database (BACKUP THIS!)
├── public/build/               # Compiled assets (npm run build)
├── storage/
│   ├── logs/                   # Application logs
│   └── framework/cache/        # Cache files
├── setup-first-time.bat        # First-time setup script
├── build-production.bat        # Production build script
├── DEPLOYMENT.md               # Full deployment guide
└── README-USER.md              # User guide for operators
```

### Important Files

| File                       | Purpose                 | Backup?            |
| -------------------------- | ----------------------- | ------------------ |
| `database/database.sqlite` | All transactions & data | ✅ YES!            |
| `.env`                     | App configuration & key | ✅ YES!            |
| `storage/logs/`            | Error logs              | ⚠️ Check regularly |
| `public/build/`            | Compiled assets         | ❌ Can rebuild     |

### Backup Strategy

```bash
# Daily backup (recommended)
copy database\database.sqlite database\backup\db-%date:~-4,4%%date:~-10,2%%date:~-7,2%.sqlite

# Manual backup
copy database\database.sqlite database\backup\database-backup-2026-02-19.sqlite
```

### Update Workflow

When code changes:

```bash
# Pull changes (if using Git)
git pull

# Run production build
build-production.bat

# Or manual
composer install --optimize-autoloader --no-dev
npm ci
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Security Checklist

- ✅ `APP_ENV=production`
- ✅ `APP_DEBUG=false`
- ✅ `APP_KEY` generated
- ✅ `.env` not in Git
- ✅ Regular database backups
- ✅ HTTPS (if deployed online)

### Performance Optimization

**Already Configured:**

- ✅ Composer autoload optimized (`--optimize-autoloader`)
- ✅ Config cached (`config:cache`)
- ✅ Routes cached (`route:cache`)
- ✅ Views compiled (`view:cache`)
- ✅ Assets minified (Vite production build)
- ✅ OPcache enabled (via Laragon PHP)

### Monitoring

```bash
# Check application info
php artisan about

# View logs
type storage\logs\laravel.log

# Check database size
dir database\database.sqlite

# Clear old logs (if needed)
del storage\logs\laravel-*.log
```

### Documentation Files

- **DEPLOYMENT.md** - Complete deployment guide with troubleshooting
- **README-USER.md** - Simple guide for end users/operators
- **AGENTS.md** - This file (technical documentation)
- **PRD.md** - Product Requirements Document
