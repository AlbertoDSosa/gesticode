<?php

namespace Database\Factories\Customers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->company();

        return [
            'name' => $name,
            'slug' => Str::of($name)->slug(),
            'email' => $this->faker->safeEmail()
        ];
    }
}
