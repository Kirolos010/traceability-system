<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'unit',
        'requires_batch_tracking',
        'is_active',
    ];

    protected $casts = [
        'requires_batch_tracking' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }
}
