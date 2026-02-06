#!/usr/bin/env python3
"""Search skill CSV data files by keyword."""

from __future__ import annotations

import argparse
import csv
from pathlib import Path

SKILL_DIR = Path(__file__).resolve().parents[1]
DATA_DIR = SKILL_DIR / "data"

FILES = {
    "filament-resources": DATA_DIR / "filament-resources.csv",
    "module-conventions": DATA_DIR / "module-conventions.csv",
    "theme-tokens": DATA_DIR / "theme-tokens.csv",
    "filament-page-patterns": DATA_DIR / "filament-page-patterns.csv",
    "laravel-commands": DATA_DIR / "laravel-commands.csv",
}


def load_rows(path: Path) -> list[dict]:
    with path.open("r", encoding="utf-8") as f:
        return list(csv.DictReader(f))


def search_rows(rows: list[dict], query: str, max_results: int) -> list[dict]:
    query_lower = query.lower()
    results = []
    for row in rows:
        hay = " ".join(str(v) for v in row.values()).lower()
        if query_lower in hay:
            results.append(row)
    return results[:max_results]


def main() -> None:
    parser = argparse.ArgumentParser(description="Search joraskill data files.")
    parser.add_argument("query", help="Search query")
    parser.add_argument("--file", choices=FILES.keys(), help="Search specific data file")
    parser.add_argument("--max-results", "-n", type=int, default=5)
    args = parser.parse_args()

    targets = [args.file] if args.file else list(FILES.keys())

    for key in targets:
        path = FILES[key]
        if not path.exists():
            continue
        rows = load_rows(path)
        matches = search_rows(rows, args.query, args.max_results)
        if not matches:
            continue
        print(f"## {key} ({len(matches)} results)")
        for i, row in enumerate(matches, 1):
            print(f"### Result {i}")
            for k, v in row.items():
                print(f"- {k}: {v}")
            print("")


if __name__ == "__main__":
    main()
