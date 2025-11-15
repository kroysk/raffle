<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Health check endpoint
$app->get('/health', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        'success' => true,
        'message' => 'ShopWire Raffle API',
        'version' => '1.0.0',
        'endpoints' => [
            'auth' => '/api/auth/*',
            'raffles' => '/api/raffles/*',
            'shopwired' => '/api/shopwired/*'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// DB health check endpoint
$app->get('/health/db', function (Request $request, Response $response) {
    try {
        $db = \App\Config\Database::getConnection();
        $stmt = $db->query('SELECT NOW() as time');
        $result = $stmt->fetch();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Database connection successful',
            'time' => $result['time']
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Database connection failed',
            'error' => $e->getMessage()
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
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