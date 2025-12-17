<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'location_id',
        'type',
        'reference_type',
        'reference_id',
        'quantity',
        'unit_cost',
        'notes',
        'user_id',
        'movement_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'movement_date' => 'datetime',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }
}
