<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user
        User::firstOrCreate(
            ['email' => 'admin@sekarlangit.com'],
            [
                'name'     => 'Admin Toko',
                'password' => Hash::make('admin123'),
            ]
        );

        // $supplier = Supplier::create([
        //     'name' => 'Pemasok Utama',
        //     'phone' => '081234567890',
        //     'address' => 'Karanggintung',
        // ]);

        // Product::create([
        //     'supplier_id' => $supplier->id,
        //     'name' => 'Gula Pasir 1kg',
        //     'barcode' => '8999999999999',
        //     'unit' => 'pcs',
        //     'price_buy' => 12000,
        //     'price_sell' => 15000,
        //     'stock' => 20,
        //     'min_stock' => 5,
        //     'active' => true,
        // ]);

        // Customer::create([
        //     'name' => 'Pelanggan Kasbon',
        //     'phone' => '081200000001',
        //     'address' => 'Karanggintung',
        // ]);
    }
}
