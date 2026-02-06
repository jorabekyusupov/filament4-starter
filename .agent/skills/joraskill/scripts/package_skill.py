#!/usr/bin/env python3
"""Package this skill into a .skill zip file."""

from __future__ import annotations

import argparse
from pathlib import Path
import zipfile


def main() -> None:
    parser = argparse.ArgumentParser(description="Package a skill directory into a .skill file.")
    parser.add_argument("--skill-dir", default=None, help="Path to skill dir (default: parent of this script)")
    parser.add_argument("--out-dir", default="dist", help="Output directory for .skill file")
    parser.add_argument("--name", default=None, help="Output file name (default: skill folder name)")
    args = parser.parse_args()

    skill_dir = Path(args.skill_dir).resolve() if args.skill_dir else Path(__file__).resolve().parents[1]
    if not (skill_dir / "SKILL.md").exists():
        raise SystemExit(f"SKILL.md not found in {skill_dir}")

    out_dir = Path(args.out_dir).resolve()
    out_dir.mkdir(parents=True, exist_ok=True)

    name = args.name or skill_dir.name
    out_file = out_dir / f"{name}.skill"

    with zipfile.ZipFile(out_file, "w", compression=zipfile.ZIP_DEFLATED) as zf:
        for path in skill_dir.rglob("*"):
            if path.is_dir():
                continue
            rel = path.relative_to(skill_dir)
            zf.write(path, rel)

    print(f"Created: {out_file}")


if __name__ == "__main__":
    main()
