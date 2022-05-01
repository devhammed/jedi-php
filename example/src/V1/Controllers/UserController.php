<?php

namespace JediExample\V1\Controllers;

use Jedi\Context\Context;

class UserController
{
    public function index()
    {
        return [
            'ok'   => true,
            'data' => [
                [
                    'id'    => 1,
                    'name'  => 'John Doe',
                    'email' => 'john@email.com',
                ],
            ],
        ];
    }

    public function show(Context $context)
    {
        return $context->request->params('id');
    }
}
