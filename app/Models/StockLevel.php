<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'location_id',
        'quantity',
        'reserved_quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'reserved_quantity' => 'decimal:4',
    ];

    // Relationships
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Helper methods
    public function getAvailableQuantityAttribute()
    {
        return $this->quantity - $this->reserved_quantity;
    }
}
