<?php

require_once __DIR__.'/../vendor/autoload.php';

use Cocur\Watchman\Watchman;

$watchman = new Watchman();
$watchman->watchLogByLevel('debug', function ($message) {
    echo "$message\n";
});
