<?php

require __DIR__ . '/../vendor/autoload.php';

$app = new \Jedi\Application();

$app->router->get('/', function () {
    return 'Hello World';
});

$app->router->get('/contact', function () {
    return '<h1>Contact Us</h1>';
});

$app->run();
