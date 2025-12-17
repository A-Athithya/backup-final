<?php
class Router {
    private $routes = [];

    public function add($method, $path, $handler) {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch($method, $uri) {
        foreach ($this->routes as $r) {
            if ($r['method'] === $method && preg_match("#^{$r['path']}$#", $uri, $matches)) {
                array_shift($matches);
                return call_user_func_array($r['handler'], $matches);
            }
        }
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
        exit;
    }
}
