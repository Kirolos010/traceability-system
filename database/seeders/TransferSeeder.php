<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Location;
use App\Models\Transfer;
use App\Services\StockService;
use Illuminate\Database\Seeder;

class TransferSeeder extends Seeder
{
    protected StockService $stockService;

    public function __construct()
    {
        $this->stockService = app(StockService::class);
    }

    public function run(): void
    {
        $batches = Batch::all();
        $warehouse = Location::where('code', 'WH-001')->first();
        $shop1 = Location::where('code', 'SHOP-001')->first();

        if ($batches->isEmpty() || !$warehouse || !$shop1) {
            $this->command->warn('Required data not found. Please run previous seeders first.');
            return;
        }

        // First, add stock to warehouse
        $batch = $batches->first();
        
        $this->stockService->recordMovement([
            'batch_id' => $batch->id,
            'location_id' => $warehouse->id,
            'type' => 'in',
            'reference_type' => 'supplier',
            'quantity' => 200,
            'movement_date' => '2024-02-10',
        ]);

        // Create transfer
        $transfer = Transfer::create([
            'transfer_number' => 'TRF-2024-001',
            'batch_id' => $batch->id,
            'from_location_id' => $warehouse->id,
            'to_location_id' => $shop1->id,
            'quantity' => 50,
            'status' => 'completed',
            'transfer_date' => '2024-02-12',
            'received_date' => '2024-02-12',
            'notes' => 'Transfer to shop',
        ]);

        // Process transfer
        $this->stockService->recordMovement([
            'batch_id' => $batch->id,
            'location_id' => $warehouse->id,
            'type' => 'out',
            'reference_type' => 'transfer',
            'reference_id' => $transfer->id,
            'quantity' => 50,
            'movement_date' => '2024-02-12',
        ]);

        $this->stockService->recordMovement([
            'batch_id' => $batch->id,
            'location_id' => $shop1->id,
            'type' => 'in',
            'reference_type' => 'transfer',
            'reference_id' => $transfer->id,
            'quantity' => 50,
            'movement_date' => '2024-02-12',
        ]);
    }
}
