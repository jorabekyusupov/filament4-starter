# Modules

## Create a module

```bash
php artisan make:module my-module
composer update modules/my-module
php artisan modules:sync
```

## Default scaffold (modular)

```
modules/
  my-module/
    composer.json
    src/
    tests/
    routes/
    resources/
    database/
```

## Project conventions

Existing modules under `modules/core/*` typically include:

- `config/config.php`
- `migrations/`
- `routes/route.php`
- `resources/views/`
- `lang/`
- `src/Providers/*ServiceProvider.php`
- `src/Models`, `src/Filament`, `src/Services`, etc.

Use `modules/core/user` as a reference when extending structure.

## Do / Don't (must follow)

- Do: extend base classes from `Modules\App\` when available.
- Do: use `BaseModel` for all Eloquent models in modules.
- Do: use `BaseDTO` for request/data mapping when DTOs are used.
- Do: use `BaseReadRepository` / `BaseWriteRepository` and interfaces for repositories.
- Do: use `BaseServiceProvider` and set `$this->name` in `boot()`.
- Do: use module base `Controller` and `BasePolicy` when applicable.
- Do: place all visual/UI strings in module JSON translations (`modules/<module>/lang/<locale>.json`).
- Don't: extend raw `Model` or `ServiceProvider` in module code unless there is a strong reason.
- Don't: hardcode UI text in Filament or Blade.

## Service provider pattern

Core modules use `Modules\App\Providers\BaseServiceProvider` and set `$this->name` in `boot()`.
Example: `modules/core/user/src/Providers/UserServiceProvider.php`.

## Model base class

When creating models in modules, extend `Modules\App\Models\BaseModel` instead of the raw Eloquent `Model`.
This base model enables activity logging via Spatie Activitylog.
File: `modules/core/app/src/Models/BaseModel.php`.
