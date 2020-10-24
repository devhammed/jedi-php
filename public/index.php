<?php

require __DIR__ . '/../vendor/autoload.php';

use Jedi\Context;
use Jedi\Application;

$app = new Application();

$app->use(function (Context $context, Closure $next) {
    try {
        return $next($context);
    } catch (Throwable $e) {
        return $context->res->status(500)->send([
            'ok' => \false,
            'message' => 'Something bad just happened.',
        ]);
    }
});

$app->group('/api', function () use ($app) {
    $app->get('/', function () {
        return [
            'ok' => true,
            'message' => 'Welcome to our API.',
        ];
    });

    $app->group('/users', function () use ($app) {
        $app->get('/', function () {
            return [1, 2, 3];
        });

        $app->get('/:user(\d+)', function (Context $context) {
            return $context->args->user;
        });
    });
});

$app->get('/', function () {
    throw new Error('dd');
    return 'f';
});

$app->get('/contact', function () {
    return '<h1>Contact Us</h1>';
});

$app->fallback(function () {
    return 'Get outta here!';
});

$app->run();
