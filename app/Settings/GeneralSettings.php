<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public array $site_name;
    public array $site_description;
    public array $site_slogan;

    public static function group(): string
    {
        return 'general-settings';
    }
}
