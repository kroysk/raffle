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

    protected function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $ruleArray = explode('|', $rule);

            foreach ($ruleArray as $r) {
                if ($r === 'required' && (!isset($data[$field]) || empty($data[$field]))) {
                    $errors[$field][] = "The {$field} field is required";
                }

                if (str_starts_with($r, 'min:')) {
                    $min = (int) str_replace('min:', '', $r);
                    if (isset($data[$field]) && strlen($data[$field]) < $min) {
                        $errors[$field][] = "The {$field} must be at least {$min} characters";
                    }
                }

                if (str_starts_with($r, 'max:')) {
                    $max = (int) str_replace('max:', '', $r);
                    if (isset($data[$field]) && strlen($data[$field]) > $max) {
                        $errors[$field][] = "The {$field} must not exceed {$max} characters";
                    }
                }

                if ($r === 'email' && isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "The {$field} must be a valid email address";
                }
            }
        }

        return $errors;
    }
}