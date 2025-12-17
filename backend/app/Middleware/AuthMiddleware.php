<?php


class AuthMiddleware {
    public static function handle() {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user']; 
        }
        
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized - Please Login']);
        exit;
    }
}
