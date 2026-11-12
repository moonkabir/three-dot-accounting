<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'phone',
        'email',
        'address',
        'opening_balance',
        'current_balance',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function bills(): HasMany
    {
        return $this->hasMany(VendorBill::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(VendorPayment::class);
    }

    public function recalculateBalance(): void
    {
        $totalBilled = $this->bills()->sum('grand_total');
        $totalPaidDirectly = $this->payments()->sum('amount');
        $this->current_balance = $this->opening_balance + $totalBilled - $totalPaidDirectly;
        $this->save();
    }
}

