<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Location;
use App\Models\Sale;
use App\Services\StockService;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    protected StockService $stockService;

    public function __construct()
    {
        $this->stockService = app(StockService::class);
    }

    public function run(): void
    {
        $batches = Batch::all();
        $shop = Location::where('code', 'SHOP-001')->first();

        if ($batches->isEmpty() || !$shop) {
            $this->command->warn('Required data not found. Please run previous seeders first.');
            return;
        }

        $batch = $batches->first();

        // Ensure there's stock in the shop (from transfer seeder)
        // If not, add some stock
        $available = $this->stockService->getAvailableStock($batch->id, $shop->id);
        if ($available < 10) {
            $this->stockService->recordMovement([
                'batch_id' => $batch->id,
                'location_id' => $shop->id,
                'type' => 'in',
                'reference_type' => 'transfer',
                'quantity' => 20,
                'movement_date' => '2024-02-13',
            ]);
        }

        // Create sale
        $sale = Sale::create([
            'sale_number' => 'SALE-2024-001',
            'batch_id' => $batch->id,
            'location_id' => $shop->id,
            'customer_name' => 'John Customer',
            'quantity' => 5,
            'unit_price' => 25.50,
            'total_amount' => 127.50,
            'sale_date' => '2024-02-15',
            'notes' => 'Customer purchase',
        ]);

        // Record stock movement
        $this->stockService->recordMovement([
            'batch_id' => $batch->id,
            'location_id' => $shop->id,
            'type' => 'out',
            'reference_type' => 'sale',
            'reference_id' => $sale->id,
            'quantity' => 5,
            'unit_price' => 25.50,
            'movement_date' => '2024-02-15',
        ]);
    }
}
