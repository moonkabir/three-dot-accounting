<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'type', // in, out, adjustment
        'quantity',
        'unit_cost',
        'reason',
        'reference_type', // sale_bill, vendor_bill, manual
        'reference_id',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

