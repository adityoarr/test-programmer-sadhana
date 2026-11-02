<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['code' => 'PCS', 'name' => 'Pcs'],
            ['code' => 'BOX', 'name' => 'Box'],
            ['code' => 'KG', 'name' => 'Kilogram'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }

        $categories = [
            ['code' => 'ATK', 'name' => 'Alat Tulis Kantor'],
            ['code' => 'ELC', 'name' => 'Elektronik'],
            ['code' => 'FAS', 'name' => 'Fasilitas'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        Warehouse::create([
            'code' => 'WH-01',
            'name' => 'Gudang Utama',
            'is_default' => true,
        ]);

        Supplier::create([
            'code' => 'SUP-001',
            'name' => 'PT Sumber Makmur',
            'email' => 'sales@sumbermakmur.co.id',
            'phone' => '021-12345678',
            'address' => 'Jakarta',
            'is_active' => true,
        ]);

        Supplier::create([
            'code' => 'SUP-002',
            'name' => 'CV Berkah Jaya',
            'email' => 'berkah@example.com',
            'phone' => '021-87654321',
            'address' => 'Bekasi',
            'is_active' => true,
        ]);

        $atk = Category::where('code', 'ATK')->first();
        $elc = Category::where('code', 'ELC')->first();

        $pcs = Unit::where('code', 'PCS')->first();
        $box = Unit::where('code', 'BOX')->first();

        Item::create([
            'code' => 'BRG-001',
            'name' => 'Kertas A4 80gsm',
            'category_id' => $atk->id,
            'unit_id' => $box->id,
            'description' => 'Kertas A4 isi 5 rim',
            'purchase_price' => 45000,
            'selling_price' => 55000,
            'min_stock' => 10,
            'is_active' => true,
        ]);

        Item::create([
            'code' => 'BRG-002',
            'name' => 'Pulpen Standard AE7',
            'category_id' => $atk->id,
            'unit_id' => $pcs->id,
            'description' => 'Pulpen tinta hitam',
            'purchase_price' => 3500,
            'selling_price' => 5000,
            'min_stock' => 50,
            'is_active' => true,
        ]);

        Item::create([
            'code' => 'BRG-003',
            'name' => 'Mouse USB',
            'category_id' => $elc->id,
            'unit_id' => $pcs->id,
            'description' => 'Mouse optical USB',
            'purchase_price' => 70000,
            'selling_price' => 95000,
            'min_stock' => 5,
            'is_active' => true,
        ]);
    }
}