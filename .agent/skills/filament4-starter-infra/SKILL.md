---
name: filament4-starter-infra
description: "Use when working on /Users/yusupov/Herd/filament4-starter for project infrastructure, module structure, Filament panels/pages/resources, or theme/Tailwind usage. Covers module creation flow, directory layout, and theme.css token usage for consistent styling."
---

# Filament4 Starter Infra

## Quick start

- Confirm you are in `/Users/yusupov/Herd/filament4-starter`.
- Prefer project conventions for modules and Filament in references below.

## When to use this skill

- When working on Filament panels/pages/resources in this project.
- When creating or structuring modules under `modules/core/*`.
- When applying Tailwind + theme token styling rules.
- When using project base classes, SOLID/OOP rules, or design patterns.

## How to use it

1. Read the relevant reference file(s) for the task.
2. Prefer scripts over manual steps when available.
3. Run scripts with `--help` first if unsure.
4. Follow Execution rules and Output expectations.

## Read these references as needed

- `references/structure.md` for project layout and key files.
- `references/modules.md` for module creation and expected structure.
- `references/filament.md` for Filament panel/page/resource patterns and theme/Tailwind rules.
- `references/base-classes.md` for base classes in `modules/core/app/src`.
- `references/solid.md` for strict SOLID principles to apply in code changes.
- `references/oop.md` for strict OOP principles to apply in code changes.
- `references/design-patterns.md` for project-oriented design patterns to apply in code changes.
- `references/ui-ux-pro-max-integration.md` for UI/UX Pro Max usage and Filament constraints.
- `references/architecture.md` for overall architecture and data-driven conventions.

## Resources

- `references/` contains detailed documentation and rules.
- `data/` contains CSV datasets for conventions and patterns.

## Scripts

- `scripts/new_module.py <module-name>`: create a module, run composer update, and sync modules.
- `scripts/new_filament_page.py <module> <PageClass>`: create Filament page class + Blade view in a module.
- `scripts/theme_tokens.py`: print Tailwind theme token mapping from `theme.css`.
- `scripts/lint_theme_usage.py`: scan Filament Blade views for theme token usage.
- `scripts/search_data.py "<query>" [--file <dataset>]`: search local CSV datasets under `data/`.
- `scripts/cheatsheet.py`: print a full conventions snapshot from CSV datasets.
- `scripts/search_data_bm25.py "<query>" [--file <dataset>]`: BM25-ranked search across CSV datasets.
- `scripts/new_filament_resource.py <module> <ResourceName> [--model <ModelClass>]`: scaffold Filament Resource in Applications-style structure.
- `scripts/package_skill.py [--skill-dir <path>] [--out-dir dist] [--name <skill-name>]`: package the skill into a `.skill` file.

## Execution rules

- Keep changes aligned with existing module conventions under `modules/core/*`.
- Prefer Tailwind utility classes in views/components; only add CSS when necessary.
- When styling Filament custom pages/components, use tokens mapped in `packages/fil-theme/resources/css/theme.css`.
- Avoid introducing new styling systems.
- When using Filament classes/components, import namespaces according to the current package structure under `vendor/filament/filament` (do not guess old namespaces).
- Always extend base classes from `Modules\App\` when available (BaseModel, BaseDTO, BaseReadRepository, BaseWriteRepository, BaseServiceProvider, BasePolicy, Controller, BaseService).
- Follow SOLID principles strictly (see `references/solid.md`).
- Follow OOP principles strictly (see `references/oop.md`).
- Follow project design patterns strictly (see `references/design-patterns.md`).

## Output expectations

- When asked to add UI: provide Blade/Filament snippets with Tailwind classes and theme tokens.
- When asked to add modules: provide command(s) and folder structure, and note any required composer update steps.
