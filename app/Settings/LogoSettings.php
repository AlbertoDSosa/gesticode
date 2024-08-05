<?php

namespace App\Settings;

use Illuminate\Support\Collection;
use Spatie\LaravelSettings\Settings;
use App\Models\Common\LogoSetting;

class LogoSettings extends Settings
{
    public array $logo;
    public array $favicon;
    public array $dark_logo;
    public array $guest_logo;
    public array $guest_background;

    public function getLogoKeys(): Collection
    {
        return $this->toCollection()->keys();
    }

    public function saveLogos($component): void
    {
        $this->getLogoKeys()->each(function($key) use($component) {
            if($component->$key) {
                $logoSetting = LogoSetting::where('group', 'logo-settings')
                ->where('name', $key)
                ->first();
                $logoSetting->clearMediaCollection($key);
                $logoSetting->addMedia($component->$key)->toMediaCollection($key);
                $this->$key = [
                    'contentType' => 'image',
                    'content' => $logoSetting->getFirstMediaUrl($key)
                ];
                $this->save();
            }
        });
    }

    public static function group(): string
    {
        return 'logo-settings';
    }
}
