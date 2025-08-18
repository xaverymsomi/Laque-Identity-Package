# KYC Scoring

The `Scoring` utility computes a 0..1 score using:
- Name similarity (Levenshtein-based)
- Date of birth exact match (weight)
- Phone number match after normalization (+255...)
- Document number match (if provided)

Interpretation:
- `score >= 0.85` → strong match
- `0.6 <= score < 0.85` → likely match, manual review
- `< 0.6` → mismatch


Operator detection uses configurable 3-digit prefixes (e.g., 071=Tigo, 075=Vodacom). Override with official TCRA lists in production.
