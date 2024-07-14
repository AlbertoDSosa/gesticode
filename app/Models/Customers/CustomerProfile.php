<?php

namespace App\Models\Customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'contact_person',
        'ref'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
