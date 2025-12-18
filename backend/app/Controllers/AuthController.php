<?php
require_once __DIR__ . '/../Services/AuthService.php';
require_once __DIR__ . '/../Services/JwtService.php';
require_once __DIR__ . '/../Middleware/CsrfMiddleware.php';
require_once __DIR__ . '/../Helpers/Response.php';

class AuthController {
    private $authService;
    private $jwtService;

    public function __construct() {
        $this->authService = new AuthService();
        $this->jwtService = new JwtService();
    }

    public function register() {
        $data = $_REQUEST['decoded_input'];
        
        // Ensure tenant_id is present (default logic or validation)
        if (!isset($data['tenant_id'])) {
            $data['tenant_id'] = 1; 
        }

        try {
            $userId = $this->authService->register($data);
            
            // Immediately log the user in after registration
            $user = $this->getUserById($userId);
            
            if (!$user) {
                throw new Exception("User registration succeeded but user record not found.");
            }

            // Generate Tokens
            $accessToken = $this->jwtService->generateAccessToken($user['id'], $user['role'], $user['tenant_id'] ?? 1);
            $refreshToken = bin2hex(random_bytes(32));
            
            // Store Refresh Token
            require_once __DIR__ . '/../Repositories/TokenRepository.php';
            $tokenRepo = new TokenRepository();
            $expiresAt = date('Y-m-d H:i:s', time() + 604800); // 7 days
            $tokenRepo->store($user['id'], $refreshToken, $expiresAt);

            // Store Access Token in PHP Session (BEST PRACTICE)
            $_SESSION['accessToken'] = $accessToken;

            Response::json([
                'message' => 'User registered and logged in', 
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'email' => $user['email'],
                    'tenant_id' => $user['tenant_id']
                ],
                'accessToken' => $accessToken,
                'refreshToken' => $refreshToken,
                'expiresIn' => 900,
                'csrfToken' => CsrfMiddleware::generate()
            ], 201);
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
            // 1. Verify Credentials
            $result = $this->authService->login($data['email'], $data['password']);
            $user = $result['user']; // [id, name, role, email, tenant_id?]
            
            // 2. Generate Tokens
            $accessToken = $this->jwtService->generateAccessToken($user['id'], $user['role'], $user['tenant_id'] ?? 1);
            $refreshToken = bin2hex(random_bytes(32));
            
            // 3. Store Refresh Token
            require_once __DIR__ . '/../Repositories/TokenRepository.php';
            $tokenRepo = new TokenRepository();
            $expiresAt = date('Y-m-d H:i:s', time() + 604800); // 7 days
            $tokenRepo->store($user['id'], $refreshToken, $expiresAt);
            
            // 4. Store Access Token in PHP Session (BEST PRACTICE)
            $_SESSION['accessToken'] = $accessToken;

            // 5. Return Encrypted Response (Response::json handles encryption)
            Response::json([
                'user' => $user,
                'accessToken' => $accessToken,
                'refreshToken' => $refreshToken,
                'expiresIn' => 900,
                'csrfToken' => CsrfMiddleware::generate()
            ]);
            
        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 401);
        }
    }
    
    public function refresh() {
        $data = $_REQUEST['decoded_input'];
        $refreshToken = $data['refreshToken'] ?? null;
        
        if (!$refreshToken) {
            Response::json(['error' => 'Refresh token required'], 400);
        }
        
        require_once __DIR__ . '/../Repositories/TokenRepository.php';
        $tokenRepo = new TokenRepository();
        
        $storedToken = $tokenRepo->isValid($refreshToken);
        
        if (!$storedToken) {
            Response::json(['error' => 'Invalid or expired refresh token'], 401);
        }
        
        // Revoke old token
        $tokenRepo->revoke($refreshToken);
        
        // Generate new tokens
        $userId = $storedToken['user_id'];
        // Fetch user role / tenant again? Ideally yes.
        require_once __DIR__ . '/../Repositories/UserRepository.php';
        $userRepo = new UserRepository();
        // We don't have findById in Repo yet?
        // Let's assume user role doesn't change often or we store it in JWT.
        // But for refresh, we should get fresh data.
        // Let's create findById in UserRepo if missing?
        // Or just use findByEmail from existing service?
        // I will add findById to UserRepo or just execute SQL here?
        // Better: Fetch user by ID.
        
        $user = $this->getUserById($userId); // Helper below
        
        if (!$user) {
             Response::json(['error' => 'User not found'], 401);
        }
        
        $newAccessToken = $this->jwtService->generateAccessToken($user['id'], $user['role'], $user['tenant_id']);
        $newRefreshToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 604800);
        
        $tokenRepo->store($userId, $newRefreshToken, $expiresAt);
        
        // Update Session with new Access Token
        $_SESSION['accessToken'] = $newAccessToken;

        Response::json([
            'accessToken' => $newAccessToken,
            'refreshToken' => $newRefreshToken,
            'expiresIn' => 900
        ]);
    }
    
    public function csrf() {
         Response::json(['csrf_token' => CsrfMiddleware::generate()]);
    }
    
    public function logout() {
        $data = $_REQUEST['decoded_input'];
        $refreshToken = $data['refreshToken'] ?? null;
        
        if ($refreshToken) {
            require_once __DIR__ . '/../Repositories/TokenRepository.php';
            $tokenRepo = new TokenRepository();
            $tokenRepo->revoke($refreshToken);
        }
        
        // Clear session data
        unset($_SESSION['accessToken']);
        session_destroy(); 
        
        Response::json(['message' => 'Logged out']);
    }
    
    private function getUserById($id) {
        require_once __DIR__ . '/../Repositories/UserRepository.php';
        return (new UserRepository())->findById($id);
    }
}
