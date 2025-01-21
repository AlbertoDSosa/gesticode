<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator
            ->add(
                'general-settings.site_name',
                ['contentType' => 'text', 'content' => 'Gesticode']
            );
        $this->migrator
            ->add(
                'general-settings.site_description',
                ['contentType' => 'text', 'content' => 'Gestión y herramientas para tus aplicaciones']
            );

        $this->migrator
            ->add(
                'general-settings.site_slogan',
                ['contentType' => 'rich_text', 'content' => '<p>Más que gestión, <strong>Confianza</strong></p>']
            );
    }
}
