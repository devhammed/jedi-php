<?php

require __DIR__ . '/vendor/autoload.php';

use Jedi\Application\Application;

$app = new Application();

$app->get('/', function () {
    return 'Hello World.';
});

$app->run();
