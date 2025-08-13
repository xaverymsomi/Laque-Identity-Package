<?php
#!/usr/bin/env php
require __DIR__ . '/../vendor/autoload.php';

use Laque\Identity\Core\IdentityService;
use Laque\Identity\Providers\MockProvider;
use Laque\Identity\Dto\IdentityQuery;
use Laque\Identity\Core\Phone;
use Laque\Identity\Core\Tin;

echo "== Phone ==\n";
echo Phone::normalize('0712 345 678'), PHP_EOL;
echo Phone::operator('0751234567'), PHP_EOL;

echo "\n== TIN ==\n";
var_dump(Tin::isValid('123-456-789'));

echo "\n== Verify (Mock) ==\n";
$svc = new IdentityService(new MockProvider());
$q = new IdentityQuery(
    nin: '19990101999999999999',
    fullName: 'Jane Doe',
    dateOfBirth: new DateTimeImmutable('1999-01-01'),
    phone: '0712 345 678',
    tin: '123-456-789'
);
print_r($svc->verify($q)->toArray());

echo "\nDone.\n";
