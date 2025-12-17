<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'batch_number',
        'lot_number',
        'manufacturing_date',
        'expiry_date',
        'supplier_id',
        'notes',
    ];

    protected $casts = [
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class, 'output_batch_id');
    }

    public function productionMaterials()
    {
        return $this->hasMany(ProductionMaterial::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Helper methods
    public function getAvailableQuantityAttribute($locationId = null)
    {
        if ($locationId) {
            $stock = $this->stockLevels()->where('location_id', $locationId)->first();
            return $stock ? ($stock->quantity - $stock->reserved_quantity) : 0;
        }

        return $this->stockLevels()->sum('quantity') - $this->stockLevels()->sum('reserved_quantity');
    }
}
