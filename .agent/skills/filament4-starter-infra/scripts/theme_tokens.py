#!/usr/bin/env python3
"""Print Tailwind theme tokens mapped from theme.css."""

from __future__ import annotations

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
THEME_CSS = REPO_ROOT / "packages" / "fil-theme" / "resources" / "css" / "theme.css"


def main() -> None:
    text = THEME_CSS.read_text(encoding="utf-8")
    m = re.search(r"@theme\s*\{([\s\S]*?)\}", text)
    if not m:
        raise SystemExit("No @theme block found in theme.css")

    block = m.group(1)
    tokens = []
    for line in block.splitlines():
        line = line.strip()
        if not line or line.startswith("/"):
            continue
        if line.startswith("--") and ":" in line:
            name = line.split(":", 1)[0].strip()
            tokens.append(name)

    print("Theme tokens:")
    for t in tokens:
        print(f"- {t}")

    print("\nSuggested Tailwind usage:")
    print("- bg-primary, text-primary")
    print("- text-link, hover:text-link-hover")
    print("- text-body")
    print("- bg-dark-bg, bg-dark-card")
    print("- rounded-[var(--radius-base)]")


if __name__ == "__main__":
    main()
