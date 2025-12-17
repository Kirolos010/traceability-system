<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_number',
        'batch_id',
        'location_id',
        'customer_location_id',
        'customer_name',
        'quantity',
        'unit_price',
        'total_amount',
        'sale_date',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'total_amount' => 'decimal:4',
        'sale_date' => 'date',
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

    public function customerLocation()
    {
        return $this->belongsTo(Location::class, 'customer_location_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
