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

    public static function patch($uri, $action, $middleware = []) {
        self::$routes['PATCH'][$uri] = ['action' => $action, 'middleware' => $middleware];
    }
    
    public static function delete($uri, $action, $middleware = []) {
        self::$routes['DELETE'][$uri] = ['action' => $action, 'middleware' => $middleware];
    }

    public static function dispatch($uri, $method) {
        if (!isset(self::$routes[$method])) {
            http_response_code(404);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        foreach (self::$routes[$method] as $routeUri => $routeConfig) {
            $matches = [];
            $isMatch = false;

            if ($uri === $routeUri) {
                $isMatch = true;
            } else {
                $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $routeUri);
                $pattern = "#^" . $pattern . "$#i";
                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches);
                    $isMatch = true;
                }
            }

            if ($isMatch) {
                // Middleware Check
                if (isset($routeConfig['middleware'])) {
                    foreach ($routeConfig['middleware'] as $mw) {
                        $parts = explode(':', $mw);
                        $mwClass = $parts[0];
                        $args = isset($parts[1]) ? explode(',', $parts[1]) : [];

                        if (class_exists($mwClass)) {
                            $result = empty($args) ? $mwClass::handle() : $mwClass::handle($args);
                            if (is_array($result)) {
                                $_REQUEST['user'] = $result; 
                            }
                        }
                    }
                }
                
                // Controller Action
                list($controllerName, $methodName) = explode('@', $routeConfig['action']);
                require_once BASE_PATH . "/app/Controllers/$controllerName.php";
                $controller = new $controllerName();
                
                call_user_func_array([$controller, $methodName], $matches);
                return;
            }
        }

        // 404 Handler
        http_response_code(404);
        echo json_encode([
            'error' => 'Route not found', 
            'requested_uri' => $uri, 
            'requested_method' => $method
        ]);
        exit;
    }
}
