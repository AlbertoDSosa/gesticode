<?php

namespace Tests\Traits;

use App\Models\Users\User;
use App\Models\Users\UserProfile;

trait UserFeatures {
    /**
     * @return User
    */
    public function createUser(array $attributes = [])
    {
        $userAttributes = collect($attributes)->only('email', 'name', 'password', 'email_verified_at')->toArray();
        $user = User::factory()->create($userAttributes);
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'first_name' => $user->name
        ]);

        $user->assignRole($attributes['role']);

        return $user;
    }

    public function makeUser(array $attributes = [])
    {
        return User::factory()->make($attributes);
    }

}
