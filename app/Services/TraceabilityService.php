<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Production;
use App\Models\ProductionMaterial;
use App\Models\Sale;
use App\Models\Transfer;
use App\Models\InventoryMovement;
use Illuminate\Support\Collection;

class TraceabilityService
{
    /**
     * Trace backward from a batch to find all source materials and suppliers
     *
     * @param int $batchId
     * @return array
     */
    public function traceBackward(int $batchId): array
    {
        $batch = Batch::with(['product', 'supplier'])->findOrFail($batchId);

        $trace = [
            'batch' => $batch,
            'supplier' => $batch->supplier,
            'raw_materials' => [],
            'production_history' => [],
        ];

        // Check if this batch was produced
        $productions = Production::where('output_batch_id', $batchId)
            ->with(['materials.batch.product', 'materials.batch.supplier'])
            ->get();

        foreach ($productions as $production) {
            $productionData = [
                'production' => $production,
                'materials' => [],
            ];

            foreach ($production->materials as $material) {
                $materialBatch = $material->batch;

                $materialData = [
                    'batch' => $materialBatch,
                    'quantity_used' => $material->quantity,
                    'supplier' => $materialBatch->supplier,
                ];

                // Recursively trace backward for each material
                $materialData['source_trace'] = $this->traceBackward($materialBatch->id);

                $productionData['materials'][] = $materialData;
            }

            $trace['production_history'][] = $productionData;
        }

        // If batch came from supplier directly (not produced)
        if ($batch->supplier && $productions->isEmpty()) {
            $trace['raw_materials'][] = [
                'batch' => $batch,
                'supplier' => $batch->supplier,
                'source' => 'direct_supplier',
            ];
        }

        return $trace;
    }

    /**
     * Trace forward from a batch to find all destinations (transfers, sales, production usage)
     *
     * @param int $batchId
     * @return array
     */
    public function traceForward(int $batchId): array
    {
        $batch = Batch::with(['product'])->findOrFail($batchId);

        $trace = [
            'batch' => $batch,
            'transfers' => [],
            'sales' => [],
            'production_usage' => [],
            'current_locations' => [],
        ];

        // Get all transfers
        $transfers = Transfer::where('batch_id', $batchId)
            ->with(['fromLocation', 'toLocation'])
            ->get();

        foreach ($transfers as $transfer) {
            $trace['transfers'][] = [
                'transfer' => $transfer,
                'from_location' => $transfer->fromLocation,
                'to_location' => $transfer->toLocation,
                'quantity' => $transfer->quantity,
                'status' => $transfer->status,
            ];
        }

        // Get all sales
        $sales = Sale::where('batch_id', $batchId)
            ->with(['location', 'customerLocation'])
            ->get();

        foreach ($sales as $sale) {
            $trace['sales'][] = [
                'sale' => $sale,
                'location' => $sale->location,
                'customer' => $sale->customerLocation ?? ['name' => $sale->customer_name],
                'quantity' => $sale->quantity,
                'sale_date' => $sale->sale_date,
            ];
        }

        // Get production usage (where this batch was used as material)
        $productionMaterials = ProductionMaterial::where('batch_id', $batchId)
            ->with(['production.product', 'production.outputBatch'])
            ->get();

        foreach ($productionMaterials as $material) {
            $production = $material->production;

            $trace['production_usage'][] = [
                'production' => $production,
                'output_product' => $production->product,
                'output_batch' => $production->outputBatch,
                'quantity_used' => $material->quantity,
                'production_date' => $production->production_date,
            ];

            // Recursively trace forward for output batch
            if ($production->output_batch_id) {
                $trace['production_usage'][count($trace['production_usage']) - 1]['output_trace'] =
                    $this->traceForward($production->output_batch_id);
            }
        }

        // Get current stock levels
        $stockLevels = $batch->stockLevels()->with('location')->get();

        foreach ($stockLevels as $stock) {
            if ($stock->quantity > 0) {
                $trace['current_locations'][] = [
                    'location' => $stock->location,
                    'quantity' => $stock->quantity,
                    'available_quantity' => $stock->available_quantity,
                ];
            }
        }

        return $trace;
    }

    /**
     * Full trace (both backward and forward)
     *
     * @param int $batchId
     * @return array
     */
    public function fullTrace(int $batchId): array
    {
        return [
            'backward_trace' => $this->traceBackward($batchId),
            'forward_trace' => $this->traceForward($batchId),
        ];
    }

    /**
     * Trace from a sale to find the batch and its complete history
     *
     * @param int $saleId
     * @return array
     */
    public function traceFromSale(int $saleId): array
    {
        $sale = Sale::with(['batch.product'])->findOrFail($saleId);

        return [
            'sale' => $sale,
            'batch_trace' => $this->fullTrace($sale->batch_id),
        ];
    }
}

