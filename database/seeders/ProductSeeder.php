<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Premium Beef Steak',
                'sku' => 'PROD-001',
                'description' => 'High quality beef steak from grass-fed cattle',
                'unit' => 'kg',
                'requires_batch_tracking' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Chicken Breast',
                'sku' => 'PROD-002',
                'description' => 'Fresh chicken breast',
                'unit' => 'kg',
                'requires_batch_tracking' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Aspirin Tablets',
                'sku' => 'PROD-003',
                'description' => '100mg aspirin tablets',
                'unit' => 'pcs',
                'requires_batch_tracking' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Meat Burger Patty',
                'sku' => 'PROD-004',
                'description' => 'Prepared burger patty made from premium beef',
                'unit' => 'pcs',
                'requires_batch_tracking' => true,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
