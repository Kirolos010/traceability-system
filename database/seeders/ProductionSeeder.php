<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Location;
use App\Models\Product;
use App\Models\Production;
use App\Models\ProductionMaterial;
use App\Services\StockService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionSeeder extends Seeder
{
    protected StockService $stockService;

    public function __construct()
    {
        $this->stockService = app(StockService::class);
    }

    public function run(): void
    {
        $products = Product::all();
        $locations = Location::where('type', 'production')->get();
        $batches = Batch::all();

        if ($products->isEmpty() || $locations->isEmpty() || $batches->isEmpty()) {
            $this->command->warn('Required data not found. Please run previous seeders first.');
            return;
        }

        $burgerProduct = $products->where('sku', 'PROD-004')->first();
        $beefBatch = $batches->where('batch_number', 'BATCH-001-2024')->first();
        $productionLocation = $locations->first();

        if (!$burgerProduct || !$beefBatch || !$productionLocation) {
            $this->command->warn('Required products/batches not found for production.');
            return;
        }

        // First, add stock to the raw material batch
        $this->stockService->recordMovement([
            'batch_id' => $beefBatch->id,
            'location_id' => $productionLocation->id,
            'type' => 'in',
            'reference_type' => 'supplier',
            'quantity' => 100, // 100 kg of beef
            'movement_date' => '2024-02-01',
        ]);

        // Create output batch for burger patties
        $outputBatch = Batch::create([
            'product_id' => $burgerProduct->id,
            'batch_number' => 'BATCH-005-2024',
            'lot_number' => 'LOT-2024-005',
            'manufacturing_date' => '2024-02-05',
            'expiry_date' => '2024-02-20',
            'notes' => 'Produced from BATCH-001-2024',
        ]);

        // Create production
        $production = Production::create([
            'production_number' => 'PROD-2024-001',
            'product_id' => $burgerProduct->id,
            'output_batch_id' => $outputBatch->id,
            'location_id' => $productionLocation->id,
            'quantity' => 500, // 500 burger patties
            'status' => 'completed',
            'production_date' => '2024-02-05',
            'notes' => 'Burger patties production',
        ]);

        // Add production materials
        ProductionMaterial::create([
            'production_id' => $production->id,
            'batch_id' => $beefBatch->id,
            'quantity' => 50, // Used 50 kg of beef
        ]);

        // Record consumption of raw materials
        $this->stockService->recordMovement([
            'batch_id' => $beefBatch->id,
            'location_id' => $productionLocation->id,
            'type' => 'out',
            'reference_type' => 'production',
            'reference_id' => $production->id,
            'quantity' => 50,
            'movement_date' => '2024-02-05',
        ]);

        // Record output stock
        $this->stockService->recordMovement([
            'batch_id' => $outputBatch->id,
            'location_id' => $productionLocation->id,
            'type' => 'in',
            'reference_type' => 'production',
            'reference_id' => $production->id,
            'quantity' => 500,
            'movement_date' => '2024-02-05',
        ]);
    }
}
