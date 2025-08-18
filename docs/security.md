# Security Notes

- Never log full MRZ or NIDA number. Mask to last 4 digits.
- Prefer short-lived tokens (if supported).
- Use rate limiting; respect provider ToS and PDPA.
- Cache positive matches per policy using PSR-16 cache (optional).


## HMAC Signing

If your contract requires request signing, set `NIDA_HMAC_SECRET`. The library will add `X-Signature: <hmac-sha256>` over the JSON payload.

## Caching

NIDA responses are cached (PSR-16) by `nidaNumber + dateOfBirth` when both are present. Use a secure cache backend in production.
