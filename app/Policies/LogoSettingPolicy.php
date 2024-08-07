<?php

namespace App\Policies;

use App\Models\Common\LogoSetting;
use App\Models\Users\User;
use Illuminate\Auth\Access\Response;

class LogoSettingPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->can('edit logo settings');
    }

}
