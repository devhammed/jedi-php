<?php

require __DIR__ . '/vendor/autoload.php';

use Jedi\Context\Context;
use Jedi\Response\Response;
use Jedi\Application\Application;
use JediExample\V1\Controllers\UserController;

$app = new Application();

$app->use(function (Context $context, Closure $next) {
    try {
        return $next();
    } catch (\Throwable $e) {
        return $context->response->json(
            [
                'ok'      => false,
                'message' => 'Something went wrong.',
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }
});

$app->fallback(function (Context $context) {
    return $context->response->json(
        [
            'ok'      => false,
            'message' => 'Resource not found.',
        ],
        Response::HTTP_NOT_FOUND,
    );
});

$app->group('/v1', function () use ($app) {
    $app->get('/', function () {
        return [
            'ok'      => true,
            'message' => 'API Version 1.',
        ];
    });

    $app->resource('/users', UserController::class);
});

$app->run();
