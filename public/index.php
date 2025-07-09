<?php

ob_start(); // Start output buffering at the very beginning

// --- ADD THESE CORS HEADERS HERE ---
// Allow requests from your frontend origin
header("Access-Control-Allow-Origin: http://localhost:5173");
// Allow specific HTTP methods
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Allow specific headers to be sent by the client
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// Allow credentials (e.g., cookies, HTTP authentication) if needed
header("Access-Control-Allow-Credentials: true");

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Respond with 204 No Content for preflight requests
    http_response_code(204);
    exit; // Stop script execution after sending preflight headers
}
// --- END CORS HEADERS ---

require_once __DIR__ . '/../bootstrap.php';

use App\GraphQL\GraphQLServer;

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->post('/graphql', [GraphQLServer::class, 'handle']);
});

$routeInfo = $dispatcher->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        header('HTTP/1.0 404 Not Found');
        echo json_encode(['error' => '404 Not Found']);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        header('HTTP/1.0 405 Method Not Allowed');
        echo json_encode(['error' => '405 Method Not Allowed', 'allowed_methods' => $allowedMethods]);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        if (is_array($handler) && count($handler) === 2 && class_exists($handler[0]) && method_exists($handler[0], $handler[1])) {
            if ((new \ReflectionMethod($handler[0], $handler[1]))->isStatic()) {
                call_user_func($handler); 
            } else {
                $controllerInstance = new $handler[0]();
                call_user_func([$controllerInstance, $handler[1]]);
            }
        } elseif (is_callable($handler)) {
            call_user_func($handler);
        } else {
            header('HTTP/1.0 500 Internal Server Error');
            echo json_encode(['error' => '500 Internal Server Error: Invalid handler']);
        }
        break;
}