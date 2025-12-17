<?php
require_once __DIR__ . '/../Services/AuthService.php';
require_once __DIR__ . '/../Helpers/Response.php';

class AuthController {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function register() {
        // Input available in $_REQUEST['decoded_input'] or global
        $data = $_REQUEST['decoded_input'];
        
        try {
            $userId = $this->authService->register($data);
            Response::json(['message' => 'User registered', 'id' => $userId], 201);
        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 400);
        }
    }

    public function login() {
        $data = $_REQUEST['decoded_input'];
        
        if (!isset($data['email']) || !isset($data['password'])) {
            Response::json(['error' => 'Email and password required'], 400);
        }

        try {
            $result = $this->authService->login($data['email'], $data['password']);
            
            // SET SESSION FOR AUTH MIDDLEWARE
            $_SESSION['user'] = $result['user'];
            
            Response::json($result);
        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 401);
        }
    }
    
    public function refresh() {
        Response::json(['message' => 'Refresh not needed (Session Auth)']);
    }
    
    public function csrf() {
         Response::json(['csrf_token' => null]);
    }
    
    public function logout() {
        session_destroy();
        Response::json(['message' => 'Logged out']);
    }
    
    public function changePassword() {
        // Implement change password logic
        Response::json(['message' => 'Change password not implemented yet']);
    }
}
