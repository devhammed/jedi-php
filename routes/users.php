<?php

use Jedi\Context;

/** @var \Jedi\Application $app */

$app->get('/', function () {
    return [1, 2, 3];
});

$app->get('/:user(\d+)', function (Context $context) {
    return $context->args->user;
});
