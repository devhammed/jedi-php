<?php

require __DIR__ . '/../vendor/autoload.php';

use Jedi\Context;
use Jedi\Application;

$app = new Application();

// Sample error handling logic
$app->use(function (Context $context, Closure $next) {
    try {
        return $next($context);
    } catch (Throwable $e) {
        return $context->res->send(
            'Something bad just happened.',
            500,
        );
    }
});

$app->get('/', function () {
    return '<h1>Home Page</h1>';
});

$app->map('ANY', '/input', function (Context $context) {
    return '<h1>Input: </h1>' . $context->req->input('h');
});

// Sample group routes
$app->group('/api', function () use ($app) {
    // Sample middleware for groups
    // This also demonstrate nested error handling 😁😁😁
    $app->use(function (Context $context, Closure $next) {
        try {
            return $next($context);
        } catch (Throwable $e) {
            return $context->res->send(
                [
                    'ok' => \false,
                    'message' => 'Something bad just happened.',
                ],
                500,
            );
        }
    });

    $app->get('/', function () {
        return [
            'ok' => true,
            'message' => 'Welcome to our API.',
        ];
    });

    $app->get('/error', function () {
        throw new Error('Testing error handling for API');

        return 'Error';
    });

    $app->group('/users', function () use ($app) {
        include_once(__DIR__ . '/../routes/users.php');
    });
});

$app->get('/contact', function () {
    return '<h1>Contact Us Page</h1>';
});

$app->get('/error', function () {
    throw new Error('Testing error handling for pages');

    return 'Error';
});

$app->fallback(function () {
    return 'Get outta here!';
});

$app->run();
