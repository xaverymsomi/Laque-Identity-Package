<?php
require __DIR__ . '/../vendor/autoload.php';

use Laque\Identity\Core\IdentityService;
use Laque\Identity\Providers\MockProvider;
use Laque\Identity\Dto\IdentityQuery;

$svc = new IdentityService(new MockProvider());

$q = new IdentityQuery(
    nin: '19990101999999999999',
    fullName: 'Jane Doe',
    dateOfBirth: new DateTimeImmutable('1999-01-01'),
    phone: '0712 345 678',
    tin: '123-456-789'
);

print_r($svc->verify($q)->toArray());
