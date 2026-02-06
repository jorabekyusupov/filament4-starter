#!/usr/bin/env python3
"""Append non-intrusive skill usage logs for analysis."""

from __future__ import annotations

import argparse
import json
from datetime import datetime, timezone
from pathlib import Path


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description="Append an English JSONL usage log entry for joraskill."
    )
    parser.add_argument(
        "--skill-name",
        default="joraskill",
        help="Skill name to log (default: joraskill).",
    )
    parser.add_argument(
        "--prompt-intent",
        required=True,
        help="What the prompt is asking for.",
    )
    parser.add_argument(
        "--work-summary",
        required=True,
        help="Summary of what was done.",
    )
    parser.add_argument(
        "--result-quality",
        required=True,
        help="How useful/complete the result is.",
    )
    parser.add_argument(
        "--prompt-quality-score",
        required=True,
        type=int,
        choices=range(1, 11),
        metavar="[1-10]",
        help="Prompt quality score on a 1-10 scale.",
    )
    parser.add_argument(
        "--prompt-quality-reason",
        required=True,
        help="One short reason for the prompt quality score.",
    )
    parser.add_argument(
        "--outcome",
        choices=("done", "partial", "blocked"),
        default="done",
        help="Task outcome status.",
    )
    parser.add_argument(
        "--notes",
        default="",
        help="Optional extra notes.",
    )
    parser.add_argument(
        "--log-file",
        default=str(Path(__file__).resolve().parents[1] / "logs" / "usage.jsonl"),
        help="Path to JSONL log file.",
    )
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    log_file = Path(args.log_file).expanduser().resolve()
    log_file.parent.mkdir(parents=True, exist_ok=True)

    entry = {
        "timestamp": datetime.now(timezone.utc).isoformat(),
        "skill_name": args.skill_name,
        "prompt_intent": args.prompt_intent,
        "work_summary": args.work_summary,
        "result_quality": args.result_quality,
        "prompt_quality_score": args.prompt_quality_score,
        "prompt_quality_reason": args.prompt_quality_reason,
        "outcome": args.outcome,
        "notes": args.notes,
    }

    with log_file.open("a", encoding="utf-8") as fh:
        fh.write(json.dumps(entry, ensure_ascii=True) + "\n")

    print(f"Log appended: {log_file}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
