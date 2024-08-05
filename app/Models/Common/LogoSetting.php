<?php

namespace App\Models\Common;

use Spatie\LaravelSettings\Models\SettingsProperty;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class LogoSetting extends SettingsProperty  implements HasMedia
{
    use InteractsWithMedia;
}
