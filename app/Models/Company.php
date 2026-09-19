<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'legal_name',
        'registration_no',
        'tax_id',
        'industry',
        'currency',
        'fiscal_year_start',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'logo_path',
    ];

    /**
     * The user who configured or owns the company settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
