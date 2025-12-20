<?php
// debug_csrf.php
define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/app/Helpers/EnvLoader.php';
EnvLoader::load(BASE_PATH . '/.env');

require_once BASE_PATH . '/app/Config/config.php';
require_once BASE_PATH . '/app/Config/database.php';
require_once BASE_PATH . '/app/Middleware/CsrfMiddleware.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/Middleware/RoleMiddleware.php';
require_once BASE_PATH . '/app/Helpers/Router.php';
require_once BASE_PATH . '/app/Helpers/Response.php';

session_start();

try {
    require_once BASE_PATH . '/app/Controllers/AuthController.php';
    $controller = new AuthController();
    $controller->csrf();
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . "\n";
    echo "LINE: " . $e->getLine() . "\n";
    echo "TRACE: " . $e->getTraceAsString() . "\n";
}
