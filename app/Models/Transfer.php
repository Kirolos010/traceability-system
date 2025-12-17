<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transfer_number',
        'batch_id',
        'from_location_id',
        'to_location_id',
        'quantity',
        'status',
        'transfer_date',
        'received_date',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'transfer_date' => 'date',
        'received_date' => 'date',
    ];

    // Relationships
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
