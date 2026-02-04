# Architecture (modules + Filament + theme)

## Modules

- Modules live under `modules/core/<module>`.
- Each module should follow base class conventions from `Modules\App\`.
- Routes are loaded via BaseServiceProvider from `routes/route.php`.

## Filament

- Admin panel configured in `app/Providers/Filament/AdminPanelProvider.php`.
- Module-specific resources/pages go in `modules/core/<module>/src/Filament/`.
- Resource structure must match `modules/core/application/src/Filament/Resources/Applications`.

## Theme

- Use tokens mapped in `packages/fil-theme/resources/css/theme.css`.
- Prefer Tailwind utilities in Blade/Filament views.
- Avoid introducing new design systems.

## Data-driven guidance

- Use `data/*.csv` to look up conventions and patterns.
- `scripts/search_data.py` for quick lookup.
- `scripts/cheatsheet.py` for full snapshot.
