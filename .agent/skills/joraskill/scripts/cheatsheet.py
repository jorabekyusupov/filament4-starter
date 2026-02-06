#!/usr/bin/env python3
"""Print a quick cheat sheet for project conventions."""

from __future__ import annotations

from pathlib import Path
import csv

SKILL_DIR = Path(__file__).resolve().parents[1]
DATA_DIR = SKILL_DIR / "data"

FILES = [
    ("Theme Tokens", DATA_DIR / "theme-tokens.csv"),
    ("Module Conventions", DATA_DIR / "module-conventions.csv"),
    ("Filament Resource Pattern", DATA_DIR / "filament-resources.csv"),
    ("Filament Page Patterns", DATA_DIR / "filament-page-patterns.csv"),
    ("Laravel Commands", DATA_DIR / "laravel-commands.csv"),
]


def read_csv(path: Path) -> list[dict]:
    with path.open("r", encoding="utf-8") as f:
        return list(csv.DictReader(f))


def main() -> None:
    for title, path in FILES:
        if not path.exists():
            continue
        print(f"\n== {title} ==")
        rows = read_csv(path)
        for row in rows:
            line = " | ".join(f"{k}: {v}" for k, v in row.items())
            print(line)


if __name__ == "__main__":
    main()
