<?php
// backend/public/index.php

// 1. Hande CORS immediately
// Allow Credentials for Session Persistence
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Max-Age: 86400");
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 2. Error Handling wrapper
try {
    ini_set('display_errors', 0); // Don't print HTML errors
    error_reporting(E_ALL);

    // Define Base Path
    define('BASE_PATH', __DIR__ . '/..');

    // Load Helpers
    if (!file_exists(BASE_PATH . '/app/Helpers/EnvLoader.php')) {
        throw new Exception("EnvLoader.php not found");
    }
    require_once BASE_PATH . '/app/Helpers/EnvLoader.php';
    EnvLoader::load(BASE_PATH . '/.env');

    require_once BASE_PATH . '/app/Config/config.php';
    require_once BASE_PATH . '/app/Config/database.php';
    
    // Load Middleware
    require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';
    require_once BASE_PATH . '/app/Middleware/CsrfMiddleware.php';
    require_once BASE_PATH . '/app/Middleware/RoleMiddleware.php';
    require_once BASE_PATH . '/app/Helpers/Router.php';

    // Start Session
    if (session_status() == PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '', 
            'secure' => false, 
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }

    // Handling Input (Encrypted)
    require_once BASE_PATH . '/app/Middleware/EncryptionMiddleware.php';
    EncryptionMiddleware::handleInput();
    
    // Input already handled by EncryptionMiddleware 

    // Routing Logic
    $raw_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = urldecode($raw_uri);
    
    $script_name = dirname($_SERVER['SCRIPT_NAME']);
    $script_name = urldecode($script_name);
    
    // Normalize slashes for comparison
    $script_name_norm = strtolower(str_replace('\\', '/', $script_name));
    $uri_norm = strtolower($uri);
    
    // Remove script dir from URI
    if (strpos($uri_norm, $script_name_norm) === 0) {
        $len = strlen($script_name_norm);
        $uri = substr($uri, $len);
    }

    $route = trim($uri, '/');

    // Load Routes
    require_once BASE_PATH . '/app/Routes/api.php';

    // Dispatch
    $method = $_SERVER['REQUEST_METHOD'];
    Route::dispatch($route, $method);

} catch (Throwable $e) {
    // Catch ANY error (Exception or Error)
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal Server Error', 
        'message' => $e->getMessage()
    ]);
}

