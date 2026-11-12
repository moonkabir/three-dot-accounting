<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, ?string $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getAllSettings(): array
    {
        $defaults = [
            'shop_name' => 'Three Dot Shop & Mart',
            'shop_tagline' => 'Retail & Wholesale Accounting',
            'shop_phone' => '+1 (555) 123-4567',
            'shop_email' => 'shop@threedot.com',
            'shop_address' => '123 Market Street, Suite 400',
            'currency_symbol' => '$',
            'invoice_terms' => 'Thank you for your business! Goods once sold can be exchanged within 7 days with valid receipt.',
        ];

        $stored = self::pluck('value', 'key')->toArray();
        return array_merge($defaults, $stored);
    }
}

