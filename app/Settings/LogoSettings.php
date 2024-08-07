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

    public function saveLogos($form): void
    {
        $this->getLogoKeys()->each(function($key) use($form) {
            if($form->$key) {
                $logoSetting = LogoSetting::where('group', 'logo-settings')
                ->where('name', $key)
                ->first();

                $logoSetting->clearMediaCollection($key);
                $logoSetting->addMedia($form->$key)->toMediaCollection($key);

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
