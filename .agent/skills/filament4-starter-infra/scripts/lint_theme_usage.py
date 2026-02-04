#!/usr/bin/env python3
"""Check Filament view files for theme token usage."""

from __future__ import annotations

from pathlib import Path


def find_repo_root(start: Path) -> Path:
    current = start.resolve()
    for _ in range(6):
        if (current / "composer.json").exists():
            return current
        current = current.parent
    raise SystemExit("Could not locate repo root (composer.json not found).")


REPO_ROOT = find_repo_root(Path(__file__).parent)
TOKEN_CLASSES = [
    "bg-primary",
    "text-primary",
    "text-link",
    "text-link-hover",
    "text-body",
    "bg-dark-bg",
    "bg-dark-card",
    "rounded-[var(--radius-base)]",
]


def scan_file(path: Path) -> bool:
    text = path.read_text(encoding="utf-8", errors="ignore")
    return any(token in text for token in TOKEN_CLASSES)


def main() -> None:
    view_dirs = [
        REPO_ROOT / "modules",
        REPO_ROOT / "app",
        REPO_ROOT / "resources",
    ]

    blade_files: list[Path] = []
    for base in view_dirs:
        if not base.exists():
            continue
        for path in base.rglob("*.blade.php"):
            if "filament" in str(path):
                blade_files.append(path)

    if not blade_files:
        print("No Filament blade files found.")
        return

    missing = [p for p in blade_files if not scan_file(p)]

    print(f"Scanned {len(blade_files)} Filament blade files.")
    if not missing:
        print("All files use at least one theme token.")
        return

    print("Files missing theme token usage:")
    for path in missing:
        print(f"- {path.relative_to(REPO_ROOT)}")


if __name__ == "__main__":
    main()
