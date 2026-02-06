#!/usr/bin/env python3
"""Create a new module using jora/modular and run standard sync steps."""

from __future__ import annotations

import argparse
import subprocess
import sys
from pathlib import Path


def find_repo_root(start: Path) -> Path:
    current = start.resolve()
    for _ in range(6):
        if (current / "composer.json").exists():
            return current
        current = current.parent
    raise SystemExit("Could not locate repo root (composer.json not found).")


REPO_ROOT = find_repo_root(Path(__file__).parent)


def run(cmd: list[str]) -> None:
    result = subprocess.run(cmd, cwd=REPO_ROOT)
    if result.returncode != 0:
        raise SystemExit(result.returncode)


def main() -> None:
    parser = argparse.ArgumentParser(description="Create a new module and sync project config.")
    parser.add_argument("name", help="Module name, e.g. my-module")
    parser.add_argument("--skip-update", action="store_true", help="Skip composer update step")
    parser.add_argument("--skip-sync", action="store_true", help="Skip modules:sync step")
    args = parser.parse_args()

    run(["php", "artisan", "make:module", args.name])

    if not args.skip_update:
        run(["composer", "update", f"modules/{args.name}"])

    if not args.skip_sync:
        run(["php", "artisan", "modules:sync"])


if __name__ == "__main__":
    main()
