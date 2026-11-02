<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'item_id',
        'warehouse_id',
        'type',
        'qty',
        'reference_type',
        'reference_id',
        'notes',
        'movement_at',
    ];

    protected $casts = [
        'movement_at' => 'datetime',
        'qty' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}