<?php

require __DIR__ . '/../vendor/autoload.php';

$app = new \Jedi\Application();

$app->router->get('/', function () {
    return 'Hello World';
});

$app->run();
