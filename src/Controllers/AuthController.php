<?php

namespace App\Controllers;

use App\Core\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

Class AuthController extends Controller {
    public function register(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody() ?? [];
        $errors = $this->validate($data, [
            'email' => 'required|email',
            'password' => 'required|min:8',
            'nickname' => 'required|min:4',
        ]);

        if (!empty($errors)) {
            return $this->error($response, 'Validation failed', 422, $errors);
        }

        $user = [
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'nickname' => $data['nickname'],
        ];

        return $this->success($response, $user, 'User registered successfully', 201);
    }

    public function login(Request $request, Response $response) : Response
    {
        return $this->success($response, 'Login');
    }
}