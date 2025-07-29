<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'number',
        'total_amount',
        'currency_code',
        'payment_method',
        'status',
        'purchase_datetime',
        'llm_name',
        'llm_text_response',
        'seller_info',
        'shopping_items',
        'invoice_edit_reason'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'purchase_datetime' => 'datetime',
            'seller_info' => 'array',
            'items' => 'array'
        ];
    }

}
