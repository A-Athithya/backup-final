<?php


class CsrfMiddleware {
    public static function generate() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function regenerate() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }

    public static function handle() {
        $headers = getallheaders();
        $token = $headers['X-CSRF-Token'] ?? $_POST['csrf_token'] ?? null;
        
        if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
            require_once __DIR__ . '/../Helpers/Response.php';
            // Response::json uses encryption too, so error will be encrypted
            Response::json(['error' => 'CSRF Token Mismatch'], 403);
            exit;
        }
    }
}
