<?php

namespace App\Models\Customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Users\User;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'email',
        'uuid'
    ];

    public function profile()
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_customers');
    }
}
