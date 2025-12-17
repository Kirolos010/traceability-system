<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
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
        return $this->hasMany(Production::class);
    }

    public function transfersFrom()
    {
        return $this->hasMany(Transfer::class, 'from_location_id');
    }

    public function transfersTo()
    {
        return $this->hasMany(Transfer::class, 'to_location_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function customerSales()
    {
        return $this->hasMany(Sale::class, 'customer_location_id');
    }
}
