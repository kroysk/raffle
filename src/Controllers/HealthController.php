<?php

namespace App\Controllers;

use App\Core\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

Class HealthController extends Controller {
    public function health(Request $request, Response $response) : Response
    {
        return $this->success($response, [
            'message' => 'API is healthy',
            'version' => '1.0.0',
            'endpoints' => [
                'auth' => '/api/auth/*',
                'raffles' => '/api/raffles/*',
                'shopwired' => '/api/shopwired/*'
            ]
        ]);
    }

    public function healthDb(Request $request, Response $response) : Response
    {
        try {
            $db = \App\Config\Database::getConnection();
            $stmt = $db->query('SELECT NOW() as time');
            $result = $stmt->fetch();
            return $this->success($response, [
                'message' => 'Database connection successful',
                'time' => $result['time']
            ]);
        } catch (\Exception $e) {
            return $this->error($response, 'Database connection failed', 500, $e->getMessage());
        }
    }
}