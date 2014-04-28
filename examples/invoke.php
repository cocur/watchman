<?php

$o = print_r($_SERVER['argv'], true);
$c = file_get_contents($_SERVER['argv'][1]);

file_put_contents(__DIR__.'/bar/log.txt', $o);
file_put_contents(__DIR__.'/bar/log.txt', $c, FILE_APPEND);

print_r($o);
echo $c."\n";
