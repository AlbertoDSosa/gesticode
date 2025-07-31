# Gesticode - Copilot Instructions

## Project Overview
Gesticode is a Laravel 11 management application with Livewire 3 + Volt, using Spatie packages for permissions, settings, media, and backups. The architecture emphasizes role-based access control, multi-language support, and modular settings management.

## Key Architecture Patterns

### Livewire Volt Structure
- **Functional Components**: Uses Volt's functional API in views (not class-based)
- **Location**: Volt components in `resources/views/livewire/pages/` with PHP logic at the top
- **Routing**: Routes defined via `Volt::route()` in route files, not controller methods
- **Example Pattern**:
```php
<?php
use function Livewire\Volt\{layout, state};
layout('layouts.app');
$variable = 'value';
?>
<div>Blade template here</div>
```

### Settings Architecture (Spatie Laravel Settings)
- **Settings Classes**: `app/Settings/` - each extends `Spatie\LaravelSettings\Settings`
- **Global Helpers**: `app/Helpers/helpers.php` provides `getGeneralSettings()`, `getLogoSettings()`
- **Storage**: Database via settings migrations in `database/settings/`
- **Array Properties**: Settings use array properties for multi-language content
- **Example**: `GeneralSettings` has `public array $site_name;` for localized values

### Permission System (Spatie Laravel Permission)
- **Middleware Pattern**: `role:admin|super-admin` for management routes
- **Permission Middleware**: `permission:show site settings|show logo settings` for specific features
- **Super Admin Gate**: `AppServiceProvider` grants super-admin all permissions automatically
- **Route Organization**: Management routes separated in `routes/management/` by domain

### Model Organization
- **Namespaced Models**: `App\Models\Users\User`, `App\Models\Common\*`
- **Relationships**: User has UserProfile, uses Spatie Media Library for avatars
- **Traits**: Models use Spatie traits (`HasRoles`, `InteractsWithMedia`)

### Route Structure
- **Domain Separation**: Management routes in separate files (`routes/management/settings.php`, `routes/management/tools.php`)
- **Middleware Groups**: Auth + role-based middleware consistently applied
- **Volt Integration**: Routes use `Volt::route()` pointing to view files, not controllers

## Development Workflows

### Asset Building
- **Frontend**: Vite 5.4+ with PostCSS (`resources/css/app.css`), TailwindCSS 3.4+, Alpine.js
- **Build Commands**: `npm run dev` for development, `npm run build` for production
- **Assets**: Multiple entry points in `vite.config.js` for modular loading
- **Laravel Vite Plugin**: v1.3+ for hot module replacement and asset versioning

### Database Operations
- **Settings Migrations**: Use `database/settings/` for Spatie settings, not regular migrations
- **Structure**: User profiles separate table, media library for file uploads
- **Permissions**: Seeded via database seeders, managed through admin interface

### Docker Development
- **Stack**: Laravel Sail with MySQL 8.0, phpMyAdmin on port 8080
- **Ports**: App on 8050, Vite on 5173, phpMyAdmin on 8080
- **Commands**: Standard Sail commands (`./vendor/bin/sail up -d`)

## Spatie Package Integration

### Key Packages in Use
- **spatie/laravel-settings**: For application configuration management
- **spatie/laravel-permission**: Role-based access control
- **spatie/laravel-medialibrary**: File uploads and media management
- **spatie/laravel-backup**: Database and file backups
- **spatie/laravel-activitylog**: User activity tracking

### Critical Patterns
- **Settings Access**: Always use helper functions, not direct class instantiation
- **Media Collections**: User avatars use 'preview' conversion (300x300)
- **Permission Checks**: Use middleware for routes, `@can` in Blades, `hasRole()` in logic
- **Activity Logging**: Automatic via Spatie traits on models

## File Conventions
- **Volt Components**: PHP logic at top, Blade template below, no class definitions
- **Settings**: Array properties for multi-language, group names match file structure
- **Routes**: Grouped by feature in separate files under `routes/management/`
- **Views**: Nested in `resources/views/livewire/pages/` matching route structure
- **Models**: Namespaced by domain (`Users/`, `Common/`)

## Common Gotchas
- **Super Admin**: Automatically has all permissions via Gate in `AppServiceProvider`
- **Volt Mounting**: Both `livewire` and `pages` directories mounted in `VoltServiceProvider`
- **Settings Migration**: Use `SettingsMigration` not regular Laravel migrations
- **Middleware Order**: Auth must come before role/permission middleware
- **Asset Loading**: Multiple entry points in Vite config for modular CSS/JS
