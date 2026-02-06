# Project Structure (filament4-starter)

## Root

- `app/Providers/Filament/AdminPanelProvider.php` configures the Filament admin panel.
- `config/app-modules.php` configures module namespace and directory (`modules`).
- `packages/fil-theme/resources/css/theme.css` defines theme tokens and overrides.
- `vite.config.js` includes Filament theme CSS in the Vite build input.
- `modules/core/*` contains core modules (user, setting, role-permission, etc.).

## Modules

Modules are stored under `modules/` (configured in `config/app-modules.php`).
Core modules live under `modules/core/` and follow a richer structure than the default modular scaffolding.

## Filament

- Panel provider: `app/Providers/Filament/AdminPanelProvider.php`
- Default panel id/path: `admin` / `/admin`
- Filament resources and pages can be discovered from `app/Filament/*` or defined inside modules.

## Build

- Vite inputs include `packages/fil-theme/resources/css/theme.css`.
- Tailwind v4 is used via `@tailwindcss/vite`.
