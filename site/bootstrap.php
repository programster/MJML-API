<?php

use iRAP\Autoloader\Autoloader;

require_once(__DIR__ . '/vendor/autoload.php');

$autoloader = new Autoloader([
    __DIR__,
    __DIR__ . '/controllers',
]);