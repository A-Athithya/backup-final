<?php
// backend/app/Middleware/RoleMiddleware.php

require_once __DIR__ . '/../Helpers/Response.php';

class RoleMiddleware {
    /**
     * Handle the middleware request.
     * 
     * @param array $allowedRoles List of roles permitted to access the route.
     * @return void
     */
    public static function handle($allowedRoles = []) {
        // AuthMiddleware must have already run and set $_REQUEST['user']
        $user = $_REQUEST['user'] ?? null;

        if (!$user) {
            Response::json(['error' => 'Unauthorized'], 401);
            exit;
        }

        $userRole = $user['role'] ?? null;

        if (!$userRole || !in_array($userRole, $allowedRoles)) {
            Response::json([
                'error' => 'Forbidden',
                'message' => 'You do not have permission to access this resource.'
            ], 403);
            exit;
        }
    }
}
