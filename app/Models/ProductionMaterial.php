<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_id',
        'batch_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
    ];

    // Relationships
    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
