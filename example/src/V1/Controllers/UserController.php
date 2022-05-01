<?php

namespace JediExample\V1\Controllers;

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
}
