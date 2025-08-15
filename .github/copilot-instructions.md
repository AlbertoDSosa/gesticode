# Gesticode - Copilot Instructions

## Project Overview
Gesticode is a Laravel 11 management application with Livewire 3 + Volt, using Spatie packages for permissions, settings, media, and backups. The architecture emphasizes role-based access control, multi-language support, and modular settings management. **Now includes PostgreSQL with pgvector for semantic search capabilities.**

## Key Architecture Patterns

### Database Stack
- **Database**: PostgreSQL 16 with pgvector extension for vector operations
- **Vector Support**: Embeddings for semantic search of invoices and documents
- **Management Interface**: pgAdmin4 (replaces phpMyAdmin) accessible on port 8080
- **Connection**: Default connection configured for `pgsql` in production environment

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

### Vector/Embeddings Architecture (NEW)
- **Invoice Embeddings**: Direct columns in `invoices` table (`content_embedding`, `metadata_embedding`)
- **Vector Utilities**: `HasVectorColumns` trait in `app/Concerns/HasVectorColumns.php`
- **Supported Operations**: L2 distance, cosine similarity, semantic search
- **Commands Available**: 
  - `php artisan vector:test` - Test pgvector operations
  - `php artisan invoices:embeddings` - Generate embeddings for invoices
  - `php artisan invoices:search "query" --user=X` - Semantic search

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
- **Invoice Model**: Enhanced with vector search capabilities and content generation
- **Relationships**: User has UserProfile, uses Spatie Media Library for avatars
- **Traits**: Models use Spatie traits (`HasRoles`, `InteractsWithMedia`) + `HasVectorColumns`

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
- **Primary DB**: PostgreSQL with pgvector extension enabled
- **Settings Migrations**: Use `database/settings/` for Spatie settings, not regular migrations
- **Vector Columns**: Use SQL direct statements for vector operations
- **Structure**: User profiles separate table, media library for file uploads, invoice embeddings
- **Permissions**: Seeded via database seeders, managed through admin interface

### Docker Development
- **Stack**: Laravel Sail with PostgreSQL 16 + pgvector, pgAdmin on port 8080
- **Ports**: App on 8050, Vite on 5173, pgAdmin on 8080, PostgreSQL on 5432
- **Commands**: Standard Sail commands (`./vendor/bin/sail up -d`)
- **Vector Extension**: Automatically enabled via init script in `docker/postgres/init-db.sh`

## Invoice Management & Search

### Invoice Model Features
- **Vector Search**: Content and metadata embeddings for semantic search
- **Searchable Content**: Auto-generated from invoice data (number, amount, items, seller info)
- **Payment Methods**: Enum values `['Desconocido', 'Targeta Bancaria', 'Efectivo']`
- **Status Values**: Enum values `['pendiente', 'aprobada', 'rechazada']`
- **Embedding Fields**: `content_embedding` (1536 dims), `metadata_embedding` (384 dims)

### Vector Operations
- **Content Embedding**: Full invoice content including items, seller, descriptions
- **Metadata Embedding**: Structured data like amount ranges, payment methods, categories
- **Search Methods**:
  - `findSimilarInvoices()` - Find similar invoices by content
  - `searchBySemantic()` - Semantic search with threshold
  - `getRelatedInvoices()` - Combined content + metadata similarity

### Commands for Vector Operations
```bash
# Test vector functionality
php artisan vector:test

# Generate embeddings for invoices
php artisan invoices:embeddings [--user=ID] [--force] [--batch=50]

# Search invoices semantically
php artisan invoices:search "query text" --user=ID [--threshold=0.7] [--limit=10]
```

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
- **Vector Operations**: Use trait methods for array ↔ vector conversions

## File Conventions
- **Volt Components**: PHP logic at top, Blade template below, no class definitions
- **Settings**: Array properties for multi-language, group names match file structure
- **Routes**: Grouped by feature in separate files under `routes/management/`
- **Views**: Nested in `resources/views/livewire/pages/` matching route structure
- **Models**: Namespaced by domain (`Users/`, `Common/`), enhanced with vector capabilities
- **Vector Utilities**: Centralized in `app/Database/Types/VectorType.php`

## Database Schema Notes

### Invoice Table Structure
```sql
-- Core invoice fields
id, user_id, number, total_amount, currency_code, payment_method, status, date
llm_text_response, seller_info, items

-- Vector fields (added via migrations)
searchable_content TEXT -- Auto-generated searchable text
content_embedding VECTOR(1536) -- Full content embedding
metadata_embedding VECTOR(384) -- Metadata embedding
embedding_generated_at TIMESTAMP -- When embedding was created
embedding_model VARCHAR -- Model used for embedding generation
```

### Vector Indexes
- `invoices_content_embedding_idx` - IVFFlat index for content vectors
- `invoices_metadata_embedding_idx` - IVFFlat index for metadata vectors  
- `invoices_user_embedding_idx` - Composite index for user + embedding filtering

## Common Gotchas
- **Super Admin**: Automatically has all permissions via Gate in `AppServiceProvider`
- **Volt Mounting**: Both `livewire` and `pages` directories mounted in `VoltServiceProvider`
- **Settings Migration**: Use `SettingsMigration` not regular Laravel migrations
- **Middleware Order**: Auth must come before role/permission middleware
- **Asset Loading**: Multiple entry points in Vite config for modular CSS/JS
- **Vector Imports**: Use `require_once` for trait in models to avoid autoload issues
- **Invoice Enums**: Use exact enum values for payment_method and status fields
- **Vector SQL**: Use raw SQL for vector operations to avoid Eloquent GROUP BY issues
- **pgAdmin Access**: Use email `admin@gesticode.com` and DB_PASSWORD for login

## Environment Configuration

### Required .env Variables
```bash
# Database
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=gesticode
DB_USERNAME=sail
DB_PASSWORD=password

# Vector/AI Integration (when implementing real embeddings)
# OPENAI_API_KEY=your_key_here
# HUGGINGFACE_TOKEN=your_token_here
```

### Development Commands
```bash
# Start services
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate

# Test vector operations
./vendor/bin/sail artisan vector:test

# Generate invoice embeddings
./vendor/bin/sail artisan invoices:embeddings --user=1

# Search invoices
./vendor/bin/sail artisan invoices:search "supermercado alimentación" --user=1
```
