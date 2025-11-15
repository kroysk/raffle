<?php
// Controllers
use App\Controllers\HealthController;
use App\Controllers\AuthController;

// Middleware
use App\Middleware\AuthMiddleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->group('/health', function ($group) {
    $group->get('', [HealthController::class, 'health']);
    $group->get('/db', [HealthController::class, 'healthDb']);
});

// Auth routes (public)
$app->group('/api/auth', function ($group) {
    $group->post('/register', [AuthController::class, 'register']);
    $group->post('/login', [AuthController::class, 'login']);
    $group->get('/me', [AuthController::class, 'me'])->add(new AuthMiddleware());
});

// 404 handler
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        'success' => false,
        'message' => 'Endpoint not found'
    ]));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
});

$app->run();