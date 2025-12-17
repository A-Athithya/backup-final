<?php
class Route {
    private static $routes = [];

    public static function get($uri, $action, $middleware = []) {
        self::$routes['GET'][$uri] = ['action' => $action, 'middleware' => $middleware];
    }

    public static function post($uri, $action, $middleware = []) {
        self::$routes['POST'][$uri] = ['action' => $action, 'middleware' => $middleware];
    }
    
    public static function put($uri, $action, $middleware = []) {
        self::$routes['PUT'][$uri] = ['action' => $action, 'middleware' => $middleware];
    }
    
    public static function delete($uri, $action, $middleware = []) {
        self::$routes['DELETE'][$uri] = ['action' => $action, 'middleware' => $middleware];
    }

    public static function dispatch($uri, $method) {
        // Debug Logging
        // Debug Logging Removed

        // Check if method exists in routes
        if (!isset(self::$routes[$method])) {

            http_response_code(404);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        foreach (self::$routes[$method] as $routeUri => $routeConfig) {
            // Convert {param} to regex
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $routeUri);
            $pattern = "#^" . $pattern . "$#i"; // Added 'i' modifier for case-insensitivity
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                
                // Debug match
                // Middleware Check
                if (isset($routeConfig['middleware'])) {
                    foreach ($routeConfig['middleware'] as $mw) {
                        if (class_exists($mw)) {
                            $mw::handle();
                        }
                    }
                }
                
                // Controller Action
                list($controllerName, $methodName) = explode('@', $routeConfig['action']);
                require_once BASE_PATH . "/app/Controllers/$controllerName.php";
                $controller = new $controllerName();
                
                // Call method with params
                call_user_func_array([$controller, $methodName], $matches);
                return;
            }
        }


        http_response_code(404);
        echo json_encode(['error' => 'Route not found', 'debug_uri' => $uri, 'debug_method' => $method]);
    }
}
