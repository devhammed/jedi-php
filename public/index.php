<?php

require __DIR__ . '/../vendor/autoload.php';

use Jedi\Context;
use Jedi\Application;

$app = new Application();

$app->service('hello', function (Context $context) {
    return $context->request->getMethod();
});

$app->router->get('/', function (Context $context) {
    return $context->hello;
});

$app->router->get('/contact', function () {
    return '<h1>Contact Us</h1>';
});

$app->router->get('/users', function (Context $context) {
    return $context->response->jsonp([1, 2, 3]);
});

$app->router->get('/users(/:user(\d+))?', function (Context $context) {
    return '<h1>Hello ' . $context->args->user . '</h1>';
});

$app->router->fallback(function () {
    return 'Get outta here!';
});

$app->run();
