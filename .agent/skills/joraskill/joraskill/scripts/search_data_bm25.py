#!/usr/bin/env python3
"""BM25 search across joraskill CSV datasets."""

from __future__ import annotations

import argparse
import csv
import re
from collections import defaultdict
from math import log
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

WEIGHTS = {
    "theme-tokens": 2.0,
}


class BM25:
    def __init__(self, k1: float = 1.5, b: float = 0.75):
        self.k1 = k1
        self.b = b
        self.corpus: list[list[str]] = []
        self.doc_lengths: list[int] = []
        self.avgdl = 0.0
        self.idf: dict[str, float] = {}
        self.doc_freqs: dict[str, int] = defaultdict(int)
        self.N = 0

    def tokenize(self, text: str) -> list[str]:
        text = re.sub(r"[^\w\s]", " ", str(text).lower())
        return [w for w in text.split() if len(w) > 2]

    def fit(self, documents: list[str]) -> None:
        self.corpus = [self.tokenize(doc) for doc in documents]
        self.N = len(self.corpus)
        if self.N == 0:
            return
        self.doc_lengths = [len(doc) for doc in self.corpus]
        self.avgdl = sum(self.doc_lengths) / self.N

        for doc in self.corpus:
            seen = set()
            for word in doc:
                if word not in seen:
                    self.doc_freqs[word] += 1
                    seen.add(word)

        for word, freq in self.doc_freqs.items():
            self.idf[word] = log((self.N - freq + 0.5) / (freq + 0.5) + 1)

    def score(self, query: str) -> list[tuple[int, float]]:
        query_tokens = self.tokenize(query)
        scores = []

        for idx, doc in enumerate(self.corpus):
            score = 0.0
            doc_len = self.doc_lengths[idx]
            term_freqs: dict[str, int] = defaultdict(int)
            for word in doc:
                term_freqs[word] += 1

            for token in query_tokens:
                if token in self.idf:
                    tf = term_freqs[token]
                    idf = self.idf[token]
                    numerator = tf * (self.k1 + 1)
                    denominator = tf + self.k1 * (1 - self.b + self.b * doc_len / self.avgdl)
                    score += idf * numerator / denominator

            scores.append((idx, score))

        return sorted(scores, key=lambda x: x[1], reverse=True)


def load_rows(path: Path) -> list[dict]:
    with path.open("r", encoding="utf-8") as f:
        return list(csv.DictReader(f))


def build_documents(rows: list[dict]) -> list[str]:
    return [" ".join(str(v) for v in row.values()) for row in rows]


def search_file(path: Path, query: str, max_results: int) -> list[tuple[dict, float]]:
    rows = load_rows(path)
    documents = build_documents(rows)
    bm25 = BM25()
    bm25.fit(documents)
    ranked = bm25.score(query)

    results: list[tuple[dict, float]] = []
    for idx, score in ranked[:max_results]:
        if score > 0:
            results.append((rows[idx], score))
    return results


def main() -> None:
    parser = argparse.ArgumentParser(description="BM25 search across joraskill datasets.")
    parser.add_argument("query", help="Search query")
    parser.add_argument("--file", choices=FILES.keys(), help="Search specific dataset")
    parser.add_argument("--max-results", "-n", type=int, default=5)
    args = parser.parse_args()

    if args.file:
        targets = [args.file]
        for key in targets:
            path = FILES[key]
            if not path.exists():
                continue
            matches = search_file(path, args.query, args.max_results)
            if not matches:
                continue
            print(f"## {key} ({len(matches)} results)")
            for i, (row, _score) in enumerate(matches, 1):
                print(f"### Result {i}")
                for k, v in row.items():
                    print(f"- {k}: {v}")
                print("")
        return

    # Global search across datasets with weights (theme-tokens prioritized)
    aggregated: list[tuple[str, dict, float]] = []
    for key, path in FILES.items():
        if not path.exists():
            continue
        weight = WEIGHTS.get(key, 1.0)
        matches = search_file(path, args.query, args.max_results)
        for row, score in matches:
            aggregated.append((key, row, score * weight))

    aggregated.sort(key=lambda x: x[2], reverse=True)
    if not aggregated:
        return

    print(f"## global ({min(len(aggregated), args.max_results)} results)")
    for i, (key, row, _score) in enumerate(aggregated[: args.max_results], 1):
        print(f"### Result {i} ({key})")
        for k, v in row.items():
            print(f"- {k}: {v}")
        print("")


if __name__ == "__main__":
    main()
