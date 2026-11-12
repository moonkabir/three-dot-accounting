<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_number',
        'vendor_reference_no',
        'vendor_id',
        'bill_date',
        'due_date',
        'subtotal',
        'discount',
        'tax',
        'grand_total',
        'paid_amount',
        'due_amount',
        'payment_status',
        'payment_method',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(VendorBillItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(VendorPayment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateNextBillNumber(): string
    {
        $prefix = 'PUR-' . date('Ymd') . '-';
        $lastBill = self::where('bill_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($lastBill) {
            $lastNumber = (int) substr($lastBill->bill_number, strlen($prefix));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }

    public function updatePaymentStatus(): void
    {
        if ($this->paid_amount >= $this->grand_total) {
            $this->payment_status = 'paid';
            $this->due_amount = 0;
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = 'partial';
            $this->due_amount = $this->grand_total - $this->paid_amount;
        } else {
            $this->payment_status = 'unpaid';
            $this->due_amount = $this->grand_total;
        }
        $this->save();
    }
}

