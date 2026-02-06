#!/usr/bin/env python3
"""Generate a Filament Page class + view under a module."""

from __future__ import annotations

import argparse
import re
from pathlib import Path


def find_repo_root(start: Path) -> Path:
    current = start.resolve()
    for _ in range(6):
        if (current / "composer.json").exists():
            return current
        current = current.parent
    raise SystemExit("Could not locate repo root (composer.json not found).")


REPO_ROOT = find_repo_root(Path(__file__).parent)


def studly(name: str) -> str:
    parts = re.split(r"[^a-zA-Z0-9]+", name.strip())
    return "".join(p.capitalize() for p in parts if p)


def kebab(name: str) -> str:
    s1 = re.sub(r"(.)([A-Z][a-z]+)", r"\1-\2", name)
    s2 = re.sub(r"([a-z0-9])([A-Z])", r"\1-\2", s1)
    return re.sub(r"[^a-zA-Z0-9]+", "-", s2).strip("-").lower()


def main() -> None:
    parser = argparse.ArgumentParser(description="Create a Filament page in a module.")
    parser.add_argument("module", help="Module name (folder under modules/core), e.g. user")
    parser.add_argument("page", help="Page class name, e.g. ReportsPage")
    parser.add_argument("--title", help="Navigation label/title", default=None)
    args = parser.parse_args()

    module = args.module.strip()
    page_class = studly(args.page)
    module_ns = studly(module)
    view_name = kebab(page_class)
    title = args.title or re.sub(r"([a-z])([A-Z])", r"\1 \2", page_class)

    page_dir = REPO_ROOT / "modules" / "core" / module / "src" / "Filament" / "Pages"
    view_dir = REPO_ROOT / "modules" / "core" / module / "resources" / "views" / "filament" / "pages"

    page_dir.mkdir(parents=True, exist_ok=True)
    view_dir.mkdir(parents=True, exist_ok=True)

    page_path = page_dir / f"{page_class}.php"
    view_path = view_dir / f"{view_name}.blade.php"

    if page_path.exists() or view_path.exists():
        raise SystemExit("Page or view already exists.")

    page_php = f"""<?php

declare(strict_types=1);

namespace Modules\\{module_ns}\\Filament\\Pages;

use BezhanSalleh\\FilamentShield\\Traits\\HasPageShield;
use Filament\\Pages\\Page;
use Illuminate\\Contracts\\Support\\Htmlable;

class {page_class} extends Page
{{
    use HasPageShield;

    protected static string|null|\\BackedEnum $navigationIcon = 'heroicon-o-rectangle-stack';

    protected string $view = '{module}::filament.pages.{view_name}';

    public static function getNavigationLabel(): string
    {{
        return __('{title}');
    }}

    public function getTitle(): string|Htmlable
    {{
        return __('{title}');
    }}
}}
"""

    view_blade = """<x-filament-panels::page>
    <div class=\"rounded-[var(--radius-base)] bg-primary/10 p-4\">
        <h2 class=\"text-body text-lg font-semibold\">{{ __('{title}') }}</h2>
        <p class=\"text-body/70 mt-2\">Replace this content.</p>
        <a class=\"text-link hover:text-link-hover\" href=\"#\">Example link</a>
    </div>
</x-filament-panels::page>
"""

    page_path.write_text(page_php, encoding="utf-8")
    view_path.write_text(view_blade, encoding="utf-8")

    print(f"Created: {page_path}")
    print(f"Created: {view_path}")


if __name__ == "__main__":
    main()
