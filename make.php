<?php

$files =
    [
        'libs/util.php',
        'libs/store.php',
        'libs/route.php',
        'libs/request.php',
    ];



$start = php_strip_whitespace('src/start.php');

$start = preg_replace('/<.*\s+.*class/m', '<?php class', $start, 1);

file_put_contents('target.php', $start);


foreach ($files as $file) {
    $txt = (php_strip_whitespace('src/' . $file));
    $txt = substr($txt, 6);
    file_put_contents('target.php', $txt, FILE_APPEND);
}

file_put_contents('signalserver', '#!/usr/bin/env php' . PHP_EOL . php_strip_whitespace(('target.php')));

unlink("target.php");
