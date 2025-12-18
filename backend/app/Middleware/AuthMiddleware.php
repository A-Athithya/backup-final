<?php


require_once __DIR__ . '/../Services/JwtService.php';

class AuthMiddleware {
    public static function handle() {
        // 1. Check PHP Session first (BEST PRACTICE)
        $token = $_SESSION['accessToken'] ?? null;
        
        // 2. Fallback to Authorization Header (Bearer Token)
        if (!$token) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? '';
            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
            }
        }

        if ($token) {
            $jwt = new JwtService();
            $decoded = $jwt->validateToken($token);
            
            if ($decoded) {
                return $decoded; // Returns payload (sub, role, tenant_id)
            }
        }
        
        require_once __DIR__ . '/../Helpers/Response.php';
        Response::json(['error' => 'Unauthorized'], 401);
        exit;
    }
}
