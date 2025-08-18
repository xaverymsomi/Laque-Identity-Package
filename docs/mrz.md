# MRZ Parsing (ICAO 9303)

Supported:
- **TD3** (Passports): 2 lines × 44 chars
- **TD1** (ID cards): 3 lines × 30 chars

Features:
- Character class enforcement ([A–Z0–9<])
- Check digit validation for document number, DoB, expiry
- Extracts: names, document number, country, nationality, DoB, sex, expiry

Example (TD3):
```
P<TZAERIKSSON<<ANNA<MARIA<<<<<<<<<<<<<<
L898902C36UTO7408122F1204159<<<<<<<<<<
```
