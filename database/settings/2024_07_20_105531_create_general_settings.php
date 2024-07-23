<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator
            ->add(
                'general-settings.logo',
                ['contentType' => 'imageSrc', 'content' => asset('/images/logo.svg')]
            );

        $this->migrator
            ->add(
                'general-settings.favicon',
                ['contentType' => 'imageSrc', 'content' => asset('/images/favicon.png')]
            );

        $this->migrator
            ->add(
                'general-settings.dark_logo',
                ['contentType' => 'imageSrc', 'content' => asset('/images/dark-logo.svg')]
            );

        $this->migrator
            ->add(
                'general-settings.guest_logo',
                ['contentType' => 'imageSrc', 'content' => asset('/images/guest-logo.svg')]
            );

        $this->migrator
            ->add(
                'general-settings.guest_background',
                ['contentType' => 'imageSrc', 'content' => asset('/images/guest-background.svg')]
            );

        $this->migrator
            ->add(
                'general-settings.site_name',
                ['contentType' => 'text', 'content' => 'Gestimas']
            );
        $this->migrator
            ->add(
                'general-settings.site_description',
                ['contentType' => 'text', 'content' => 'Gestión y herramientas para tus aplicaciones']
            );
    }
}
