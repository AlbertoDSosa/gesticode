<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Users\User;


class Invoice extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'number',
        'total_amount',
        'currency_code',
        'payment_method',
        'status',
        'time',
        'date',
        'llm_name',
        'llm_text_response',
        'seller_info',
        'items'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('invoices')
            ->singleFile()
            ->useDisk('invoices')
            ->acceptsMimeTypes(['image/jpeg', 'image/jpg']);

    }

    /**
     * Get the user that owns the invoice.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
