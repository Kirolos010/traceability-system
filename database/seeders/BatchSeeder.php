<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class BatchSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $suppliers = Supplier::all();

        if ($products->isEmpty() || $suppliers->isEmpty()) {
            $this->command->warn('Products or Suppliers not found. Please run ProductSeeder and SupplierSeeder first.');
            return;
        }

        $batches = [
            [
                'product_id' => $products->where('sku', 'PROD-001')->first()->id,
                'batch_number' => 'BATCH-001-2024',
                'lot_number' => 'LOT-2024-001',
                'manufacturing_date' => '2024-01-15',
                'expiry_date' => '2024-04-15',
                'supplier_id' => $suppliers->where('code', 'SUP-001')->first()->id,
                'notes' => 'Premium quality batch',
            ],
            [
                'product_id' => $products->where('sku', 'PROD-001')->first()->id,
                'batch_number' => 'BATCH-002-2024',
                'lot_number' => 'LOT-2024-002',
                'manufacturing_date' => '2024-02-01',
                'expiry_date' => '2024-05-01',
                'supplier_id' => $suppliers->where('code', 'SUP-001')->first()->id,
                'notes' => 'Second batch from supplier',
            ],
            [
                'product_id' => $products->where('sku', 'PROD-002')->first()->id,
                'batch_number' => 'BATCH-003-2024',
                'lot_number' => 'LOT-2024-003',
                'manufacturing_date' => '2024-01-20',
                'expiry_date' => '2024-02-20',
                'supplier_id' => $suppliers->where('code', 'SUP-001')->first()->id,
                'notes' => 'Fresh chicken batch',
            ],
            [
                'product_id' => $products->where('sku', 'PROD-003')->first()->id,
                'batch_number' => 'BATCH-004-2024',
                'lot_number' => 'LOT-2024-004',
                'manufacturing_date' => '2024-01-10',
                'expiry_date' => '2026-01-10',
                'supplier_id' => $suppliers->where('code', 'SUP-002')->first()->id,
                'notes' => 'Pharmaceutical batch',
            ],
        ];

        foreach ($batches as $batch) {
            Batch::create($batch);
        }
    }
}
