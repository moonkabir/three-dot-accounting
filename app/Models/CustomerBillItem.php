<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerBillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_bill_id',
        'item_id',
        'item_name',
        'quantity',
        'unit_price',
        'unit_cost',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function customerBill(): BelongsTo
    {
        return $this->belongsTo(CustomerBill::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}

