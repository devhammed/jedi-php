<?php

require __DIR__ . '/../vendor/autoload.php';

use Jedi\Context;
use Jedi\Application;

$app = new Application();

$app->use(function (Context $ctx, callable $next) {
    echo 'start<br />';

    echo $next($ctx) . '<br/>';

    echo 'end<br/>';
});

$app->get('/', function () {
    return 'Hello World!';
});

$app->get('/contact', function () {
    return '<h1>Contact Us</h1>';
});

$app->get('/users', function (Context $context) {
    return $context->response->jsonp([1, 2, 3]);
});

$app->get('/users(/:user(\d+))?', function (Context $context) {
    return '<h1>Hello ' . $context->args->user . '</h1>';
});

$app->fallback(function () {
    return 'Get outta here!';
});

$app->run();
