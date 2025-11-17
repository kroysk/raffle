<?php
// Controllers
use App\Controllers\HealthController;
use App\Controllers\AuthController;
use App\Controllers\ShopWiredAccountController;
use App\Controllers\RaffleController;
use App\Controllers\RaffleEntryController;
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

// Vue routes (public)
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(file_get_contents(__DIR__ . '/index.html'));
    return $response;
});

// Auth routes (public)
$app->group('/api/auth', function ($group) {
    $group->post('/register', [AuthController::class, 'register']);
    $group->post('/login', [AuthController::class, 'login']);
    $group->get('/me', [AuthController::class, 'me'])->add(new AuthMiddleware());
});

// ShopWired routes
$app->group('/api/shopwired', function ($group) {
    $group->post('/accounts', [ShopWiredAccountController::class, 'create']);
    $group->get('/accounts', [ShopWiredAccountController::class, 'findAll']);
    $group->delete('/accounts/{id}', [ShopWiredAccountController::class, 'delete']);
})->add(new AuthMiddleware());

// Raffles routes
$app->group('/api/raffles', function ($group) {
    $group->post('', [RaffleController::class, 'create']);
    $group->get('', [RaffleController::class, 'findAll']);
    $group->get('/{id}', [RaffleController::class, 'find']);
    $group->get('/{id}/entries', [RaffleEntryController::class, 'findAll']);
    $group->get('/{id}/entries/export', [RaffleEntryController::class, 'exportCsv']);
})->add(new AuthMiddleware());

$app->post('/api/raffles/webhook', [RaffleEntryController::class, 'webhook']);

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