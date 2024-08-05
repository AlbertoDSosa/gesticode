<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateLogoSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator
            ->add(
                'logo-settings.logo',
                ['contentType' => 'imageSrc', 'content' => asset('/images/logo.svg')]
            );

        $this->migrator
            ->add(
                'logo-settings.favicon',
                ['contentType' => 'imageSrc', 'content' => asset('/images/favicon.png')]
            );

        $this->migrator
            ->add(
                'logo-settings.dark_logo',
                ['contentType' => 'imageSrc', 'content' => asset('/images/dark-logo.svg')]
            );

        $this->migrator
            ->add(
                'logo-settings.guest_logo',
                ['contentType' => 'imageSrc', 'content' => asset('/images/guest-logo.svg')]
            );

        $this->migrator
            ->add(
                'logo-settings.guest_background',
                ['contentType' => 'imageSrc', 'content' => asset('/images/guest-background.svg')]
            );
    }
}
