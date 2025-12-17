<?php
class AuthMiddleware {
    public static function handle() {
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!$token || !JWT::verify(str_replace("Bearer ","",$token))) {
            Response::error("Unauthorized", 401);
        }
    }
}
