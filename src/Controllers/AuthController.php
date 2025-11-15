<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\JWT;
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
            $token = JWT::encode(['user_id' => $user]);
            return $this->success($response, [
                'id' => $user,
                'email' => $data['email'],
                'nickname' => $data['nickname'],
                'token' => $token,
            ], 'User registered successfully', 201);
        } catch (\Exception $e) {
            return $this->error($response, 'Failed to register user', 500, $e->getMessage());
        }
    }

    public function login(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody() ?? [];
        $errors = $this->validate($data, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if(!empty($errors)) {
            return $this->error($response, 'Validation failed', 422, $errors);
        }

        $user = $this->userModel->findByEmail($data['email']);
        if (!$user || !$this->userModel->verifyPassword($data['password'], $user['password_hash'])) {
            return $this->error($response, 'Invalid credentials', 401);
        }
        $token = JWT::encode(['user_id' => $user['id']]);
        
        return $this->success($response, [
            'id' => $user['id'],
            'email' => $user['email'],
            'nickname' => $user['nickname'],
            'token' => $token,
        ], 'Login successful', 200);
    }
}