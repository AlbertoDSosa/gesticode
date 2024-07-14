<?php

namespace Tests\Traits;

use App\Models\Customers\Customer;
use App\Models\Customers\CustomerProfile;

trait CustomerFeatures {
    public function createCustomer(array $attributes = [])
    {
        $customer = Customer::factory()->create($attributes);

        CustomerProfile::factory()->create([
            'customer_id' => $customer->id
        ]);
        return $customer;
    }
}
