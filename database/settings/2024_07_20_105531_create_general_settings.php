<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
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
