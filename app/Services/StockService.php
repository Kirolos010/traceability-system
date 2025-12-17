<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Location;
use App\Models\StockLevel;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Update stock level for a batch at a location
     *
     * @param int $batchId
     * @param int $locationId
     * @param float $quantityChange (positive for increase, negative for decrease)
     * @param float $reservedChange (optional, for reserving stock)
     * @return StockLevel
     * @throws \Exception
     */
    public function updateStock(int $batchId, int $locationId, float $quantityChange, float $reservedChange = 0): StockLevel
    {
        return DB::transaction(function () use ($batchId, $locationId, $quantityChange, $reservedChange) {
            $stockLevel = StockLevel::firstOrCreate(
                [
                    'batch_id' => $batchId,
                    'location_id' => $locationId,
                ],
                [
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                ]
            );

            $newQuantity = $stockLevel->quantity + $quantityChange;
            $newReserved = $stockLevel->reserved_quantity + $reservedChange;

            // Prevent negative stock
            if ($newQuantity < 0) {
                throw new \Exception("Insufficient stock. Available: {$stockLevel->quantity}, Requested: " . abs($quantityChange));
            }

            if ($newReserved < 0) {
                throw new \Exception("Invalid reserved quantity");
            }

            // Prevent reserved quantity from exceeding available quantity
            if ($newReserved > $newQuantity) {
                throw new \Exception("Reserved quantity cannot exceed available quantity");
            }

            $stockLevel->quantity = $newQuantity;
            $stockLevel->reserved_quantity = $newReserved;
            $stockLevel->save();

            return $stockLevel;
        });
    }

    /**
     * Record inventory movement and update stock
     *
     * @param array $movementData
     * @return InventoryMovement
     * @throws \Exception
     */
    public function recordMovement(array $movementData): InventoryMovement
    {
        return DB::transaction(function () use ($movementData) {
            $quantity = $movementData['quantity'];
            $type = $movementData['type'];

            // For 'out' movements, quantity should be negative
            if ($type === 'out') {
                $quantityChange = -abs($quantity);
            } elseif ($type === 'in') {
                $quantityChange = abs($quantity);
            } else {
                // adjustment can be positive or negative
                $quantityChange = $quantity;
            }

            // Update stock level
            $this->updateStock(
                $movementData['batch_id'],
                $movementData['location_id'],
                $quantityChange
            );

            // Create inventory movement record
            $movement = InventoryMovement::create([
                'batch_id' => $movementData['batch_id'],
                'location_id' => $movementData['location_id'],
                'type' => $movementData['type'],
                'reference_type' => $movementData['reference_type'] ?? null,
                'reference_id' => $movementData['reference_id'] ?? null,
                'quantity' => abs($quantity),
                'unit_cost' => $movementData['unit_cost'] ?? null,
                'notes' => $movementData['notes'] ?? null,
                'user_id' => $movementData['user_id'] ?? null,
                'movement_date' => $movementData['movement_date'] ?? now(),
            ]);

            return $movement;
        });
    }

    /**
     * Get available stock for a batch at a location
     *
     * @param int $batchId
     * @param int $locationId
     * @return float
     */
    public function getAvailableStock(int $batchId, int $locationId): float
    {
        $stockLevel = StockLevel::where('batch_id', $batchId)
            ->where('location_id', $locationId)
            ->first();

        if (!$stockLevel) {
            return 0;
        }

        return $stockLevel->quantity - $stockLevel->reserved_quantity;
    }

    /**
     * Reserve stock for a pending operation
     *
     * @param int $batchId
     * @param int $locationId
     * @param float $quantity
     * @return StockLevel
     * @throws \Exception
     */
    public function reserveStock(int $batchId, int $locationId, float $quantity): StockLevel
    {
        $available = $this->getAvailableStock($batchId, $locationId);

        if ($available < $quantity) {
            throw new \Exception("Insufficient available stock. Available: {$available}, Requested: {$quantity}");
        }

        return $this->updateStock($batchId, $locationId, 0, $quantity);
    }

    /**
     * Release reserved stock
     *
     * @param int $batchId
     * @param int $locationId
     * @param float $quantity
     * @return StockLevel
     */
    public function releaseReservedStock(int $batchId, int $locationId, float $quantity): StockLevel
    {
        return $this->updateStock($batchId, $locationId, 0, -abs($quantity));
    }
}

