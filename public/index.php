<?php

require __DIR__ . '/../vendor/autoload.php';

use Jedi\Context;
use Jedi\Application;

$app = new Application();

$app->group('/api', function () use ($app) {
    $app->get('/', function (Context $context) {
        return $context->response->json([
            'ok' => true,
            'message' => 'Welcome to our API'
        ]);
    });

    $app->group('/users', function () use ($app) {
        $app->get('/', function (Context $context) {
            return $context->response->json([1, 2, 3]);
        });

        $app->get('/:user(\d+)', function (Context $context) {
            return [1, 2, 3];
        });
    });
});

$app->get('/', function () {
    return 'Hello World!';
});

$app->get('/contact', function () {
    return '<h1>Contact Us</h1>';
});

$app->fallback(function () {
    return 'Get outta here!';
});

$app->run();
