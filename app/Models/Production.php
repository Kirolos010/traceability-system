<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Production extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'production_number',
        'product_id',
        'output_batch_id',
        'location_id',
        'quantity',
        'status',
        'production_date',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'production_date' => 'date',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function outputBatch()
    {
        return $this->belongsTo(Batch::class, 'output_batch_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function materials()
    {
        return $this->hasMany(ProductionMaterial::class);
    }
}
