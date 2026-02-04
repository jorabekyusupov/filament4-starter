# UI/UX Pro Max Integration (must follow)

Use the `ui-ux-pro-max` skill for any UI/UX design, layout, or visual system work.
This project stores skills under `.agent/skills/`.

## Correct commands (repo root)

```bash
python3 .agent/skills/ui-ux-pro-max/scripts/search.py "<query>" --design-system
python3 .agent/skills/ui-ux-pro-max/scripts/search.py "<query>" --design-system --persist -p "Project Name"
python3 .agent/skills/ui-ux-pro-max/scripts/search.py "<query>" --domain ux
python3 .agent/skills/ui-ux-pro-max/scripts/search.py "<query>" --stack html-tailwind
```

## Filament-specific constraints

- Always map design system colors to Filament theme tokens in `packages/fil-theme/resources/css/theme.css`.
- Prefer Tailwind utilities in Blade/Filament views.
- Do not introduce new color systems or fonts without checking existing theme tokens.
