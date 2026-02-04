# Filament

## Panel configuration

Panel provider: `app/Providers/Filament/AdminPanelProvider.php`.

Key defaults:

- id: `admin`
- path: `/admin`
- SPA enabled via `->spa()`
- custom login page: `Modules\User\Filament\Pages\CustomLogin`
- plugins: FilamentShield, FilTheme, ModuleConnectService, ActivityLog

## Custom pages/resources

- Prefer placing module-specific Filament pages/resources under the module, e.g.
  `modules/core/<module>/src/Filament/...`.
- If using app-level resources, follow `app/Filament/...` conventions.

## Resource structure (must follow)

Use the Applications resource as the template:

```
modules/core/<module>/src/Filament/Resources/<ResourceName>/
  <ResourceName>Resource.php
  Pages/
    List<ResourceNamePlural>.php
    Create<ResourceName>.php
    Edit<ResourceName>.php
  Schemas/
    <ResourceName>Form.php
    <ResourceName>Infolist.php
  Tables/
    <ResourceNamePlural>Table.php
```

Example: `modules/core/application/src/Filament/Resources/Applications/*`.

## Theme + Tailwind rules (must follow)

### Theme tokens

`packages/fil-theme/resources/css/theme.css` maps CSS variables to Tailwind tokens via `@theme`.
Use these Tailwind classes:

- `bg-primary`, `text-primary` (maps to `--primary-200`)
- `text-link`, `hover:text-link-hover`
- `text-body`
- `bg-dark-bg`, `bg-dark-card`
- `rounded-[var(--radius-base)]`

### Usage examples

```html
<div class="rounded-[var(--radius-base)] bg-primary text-white p-4">
  ...
</div>

<a class="text-link hover:text-link-hover">Link</a>
```

### Guidance

- Prefer Tailwind utilities for layout and spacing.
- Only add custom CSS if Tailwind cannot express it.
- Keep styles consistent with theme tokens defined in `theme.css`.

## Namespace rule (must follow)

- When using Filament resources/pages/forms/tables/infolists/actions/components, use namespaces from the currently installed Filament package under `vendor/filament/filament`.
- Do not rely on outdated imports from older Filament versions.
- If unsure, check existing project usage first (e.g. `modules/core/application/src/Filament/Resources/Applications/*`) and then confirm against `vendor/filament/filament`.
