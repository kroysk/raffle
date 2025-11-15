<?php

namespace App\Controllers;

use App\Core\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\User;

Class AuthController extends Controller {

    private User $userModel;
    public function __construct()
    {
        $this->userModel = new User();
    }
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

        // Check if user already exists
        $user = $this->userModel->findByEmail($data['email']);
        if ($user) {
            return $this->error($response, 'User already exists', 409);
        }

        try {
            $user = $this->userModel->createUser(
                $data['email'],
                $data['password'],
                $data['nickname']
            );

            return $this->success($response, [
                'id' => $user,
                'email' => $data['email'],
                'nickname' => $data['nickname'],
            ], 'User registered successfully', 201);
        } catch (\Exception $e) {
            return $this->error($response, 'Failed to register user', 500, $e->getMessage());
        }
    }

    public function login(Request $request, Response $response) : Response
    {
        return $this->success($response, 'Login');
    }
}