#!/usr/bin/env php
<?php
// tools/update-composer-scripts.php
$path = __DIR__ . '/../composer.json';
if (!is_file($path)) {
    fwrite(STDERR, "composer.json not found. Run this from project root.\n");
    exit(1);
}
$composer = json_decode(file_get_contents($path), true);
if (!is_array($composer)) {
    fwrite(STDERR, "composer.json is invalid JSON.\n");
    exit(1);
}
if (!isset($composer['scripts']) || !is_array($composer['scripts'])) {
    $composer['scripts'] = [];
}
$composer['scripts']['test:smoke'] = 'php bin/smoke.php';
$composer['scripts']['test:unit'] = 'vendor/bin/phpunit --colors=always';

file_put_contents($path, json_encode($composer, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) . PHP_EOL);
echo "Updated composer.json with scripts:\n";
echo " - composer test:smoke\n";
echo " - composer test:unit\n";
