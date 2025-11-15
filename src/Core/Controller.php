<?php

namespace App\Core;

use Psr\Http\Message\ResponseInterface as Response;

abstract class Controller 
{
    protected function json(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    protected function success(Response $response, $data = null, string $message = 'Success', int $status = 200): Response
    {
        return $this->json($response, [
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }
    
    protected function error(Response $response, string $message, int $status = 400, $errors = null): Response
    {
        return $this->json($response, [
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}