<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public array $logo;
    public array $favicon;
    public array $dark_logo;
    public array $guest_logo;
    public array $guest_background;
    public array $site_name;
    public array $site_description;

    public static function group(): string
    {
        return 'general-settings';
    }
}
