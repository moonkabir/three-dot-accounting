<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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
        return $this->hasMany(CustomerBill::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function recalculateBalance(): void
    {
        // current_balance = opening_balance + sum(bills.due_amount) or sum(bills.grand_total) - sum(all payments)
        // More directly: bills total due or sum of unpaid amounts:
        $totalBilled = $this->bills()->sum('grand_total');
        $totalPaidDirectly = $this->payments()->sum('amount');
        $this->current_balance = $this->opening_balance + $totalBilled - $totalPaidDirectly;
        $this->save();
    }
}

