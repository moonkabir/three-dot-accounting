<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'unit',
        'cost_price',
        'selling_price',
        'stock_quantity',
        'alert_quantity',
        'description',
        'status',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock_quantity' => 'decimal:2',
        'alert_quantity' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function customerBillItems(): HasMany
    {
        return $this->hasMany(CustomerBillItem::class);
    }

    public function vendorBillItems(): HasMany
    {
        return $this->hasMany(VendorBillItem::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_quantity <= $this->alert_quantity;
    }

    public function getMarginPercentageAttribute(): float
    {
        if ($this->cost_price <= 0) {
            return 100.0;
        }
        return round((($this->selling_price - $this->cost_price) / $this->cost_price) * 100, 1);
    }
}

