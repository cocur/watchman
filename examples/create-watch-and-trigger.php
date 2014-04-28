<?php

require_once __DIR__.'/../vendor/autoload.php';

use Cocur\Watchman\Watchman;

$fooDir = __DIR__.'/foo';
$barDir = __DIR__.'/bar';
$invoke = __DIR__.'/invoke.php';

if (!file_exists($fooDir)) {
    mkdir($fooDir);
}
if (!file_exists($barDir)) {
    mkdir($barDir);
}

$watchman = new Watchman();
$watch = $watchman->addWatch($fooDir);
$trigger = $watch->addTrigger('textfiles', '*.txt', "php $invoke");

echo "Added watch and trigger\n";

sleep(1);

file_put_contents($fooDir.'/1.txt', 'Hello '.uniqid()."\n");

sleep(1);

echo "Reading log file:\n";

echo file_get_contents($barDir.'/log.txt');

$trigger->delete();
$watch->delete();
