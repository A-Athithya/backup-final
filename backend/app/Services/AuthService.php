<?php
require_once __DIR__ . '/../Repositories/UserRepository.php';
require_once __DIR__ . '/../Repositories/TokenRepository.php';
require_once __DIR__ . '/../Repositories/TokenRepository.php';
require_once __DIR__ . '/../Config/config.php';

class AuthService {
    private $userRepo;
    private $tokenRepo;

    public function __construct() {
        $this->userRepo = new UserRepository();
        $this->tokenRepo = new TokenRepository();
    }

    public function register($data) {
        $existing = $this->userRepo->findByEmail($data['email']);
        if ($existing) {
            throw new Exception("Email already registered");
        }

        // 1. Create User
        $userId = $this->userRepo->create($data); // Creates in 'users' table

        // 2. Create Role Profile
        // We need to inject or instantiate services. For simplicity in this non-DI setup, we instantiate here.
        // In a real app, use dependency injection.
        
        $role = $data['role'] ?? 'patient';

        if ($role === 'patient') {
            require_once __DIR__ . '/PatientService.php';
            $patientService = new PatientService();
            // Pass the same data array, which contains all profile fields
            $patientService->createPatient($data);
        } elseif ($role === 'admin') {
            // Admin only exists in users table, no separate profile table needed
            return $userId;
        } else {
            // Assume it's staff
            require_once __DIR__ . '/StaffService.php';
            $staffService = new StaffService();
            $staffService->createStaff($data);
        }

        return $userId;
    }

    public function login($email, $password) {
        $user = $this->userRepo->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid credentials");
        }

        // Plaintext Login - returning user data directly
        return [
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'role' => $user['role'],
                'email' => $user['email']
            ]
        ];
    }
}
