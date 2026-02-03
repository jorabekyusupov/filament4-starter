---
name: filament4-starter-infra
description: "Use when working on /Users/yusupov/Herd/filament4-starter for project infrastructure, module structure, Filament panels/pages/resources, or theme/Tailwind usage. Covers module creation flow, directory layout, and theme.css token usage for consistent styling."
---

# Filament4 Starter Infra

## Quick start

- Confirm you are in `/Users/yusupov/Herd/filament4-starter`.
- Prefer project conventions for modules and Filament in references below.

## Read these references as needed

- `references/structure.md` for project layout and key files.
- `references/modules.md` for module creation and expected structure.
- `references/filament.md` for Filament panel/page/resource patterns and theme/Tailwind rules.
- `references/base-classes.md` for base classes in `modules/core/app/src`.
- `references/solid.md` for strict SOLID principles to apply in code changes.
- `references/oop.md` for strict OOP principles to apply in code changes.
- `references/design-patterns.md` for project-oriented design patterns to apply in code changes.
- `references/ui-ux-pro-max-integration.md` for UI/UX Pro Max usage and Filament constraints.

## Scripts

- `scripts/new_module.py <module-name>`: create a module, run composer update, and sync modules.
- `scripts/new_filament_page.py <module> <PageClass>`: create Filament page class + Blade view in a module.
- `scripts/theme_tokens.py`: print Tailwind theme token mapping from `theme.css`.
- `scripts/lint_theme_usage.py`: scan Filament Blade views for theme token usage.

## Execution rules

- Keep changes aligned with existing module conventions under `modules/core/*`.
- Prefer Tailwind utility classes in views/components; only add CSS when necessary.
- When styling Filament custom pages/components, use tokens mapped in `packages/fil-theme/resources/css/theme.css`.
- Avoid introducing new styling systems.
- Always extend base classes from `Modules\App\` when available (BaseModel, BaseDTO, BaseReadRepository, BaseWriteRepository, BaseServiceProvider, BasePolicy, Controller, BaseService).
- Follow SOLID principles strictly (see `references/solid.md`).
- Follow OOP principles strictly (see `references/oop.md`).
- Follow project design patterns strictly (see `references/design-patterns.md`).

## Output expectations

- When asked to add UI: provide Blade/Filament snippets with Tailwind classes and theme tokens.
- When asked to add modules: provide command(s) and folder structure, and note any required composer update steps.
