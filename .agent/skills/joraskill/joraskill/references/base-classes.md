# Base Classes (modules/core/app/src)

## Models

- `Modules\App\Models\BaseModel`
  - Extends Eloquent `Model`
  - Enables Spatie Activitylog via `LogsActivity`
  - Use as the base for module models

## DTO

- `Modules\App\DTO\BaseDTO`
  - `fromArray()` and `fromRequest()` helpers
  - Populates only declared properties

## Repositories

- `Modules\App\Repositories\BaseReadRepository`
  - Common read helpers like `getActiveEntities()` and `find()`
  - Expects `$this->model` in child classes
  - Uses translation helpers for `name` and locale

- `Modules\App\Repositories\BaseWriteRepository`
  - `filterNullAndEmpty()` helper

Interfaces:
- `BaseReadRepositoryInterface`
- `BaseWriteRepositoryInterface`

## Providers

- `Modules\App\Providers\BaseServiceProvider`
  - Loads migrations, routes, configs, translations, views
  - Modules set `$this->name` in `boot()`

## Policies / Controllers / Seeders

- `Modules\App\Policies\BasePolicy` (empty base)
- `Modules\App\Controllers\Controller` (empty base)
- `Modules\App\Seeders\BaseService` (empty base)

## Services

- `Modules\App\Services\ExceptionSenderService`
  - Sends exception details to Telegram
  - Strips sensitive request data
  - Adds Filament context if present
