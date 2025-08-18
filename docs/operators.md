# Operator Detection (Tanzania)

This library can detect the **mobile network operator** by the first 3 digits of the Tanzanian NSN (after `+255`).

> **Note:** The defaults are indicative. Always verify against **TCRA** official allocations and override via the second parameter of `Operator::detect()`.

Default mapping (subject to change; override in production):

- `071`, `067` → **Tigo**
- `075`, `074`, `076` → **Vodacom**
- `078`, `068`, `069`, `065` → **Airtel**
- `062` → **Halotel**
- `073` → **TTCL**
- `077` → **Zantel**

Example:

```php
use Laque\Identity\Core\Operator;

$op = Operator::detect('0712 345 678'); // "Tigo"
$op2 = Operator::detect('+255752000000'); // "Vodacom"
$op3 = Operator::detect('0782 000 000'); // "Airtel"
```
