<?php
require_once __DIR__ . '/../Helpers/Response.php';

require_once __DIR__ . '/../Repositories/UserRepository.php';

class UserController {
    private $userRepo;

    public function __construct() {
        $this->userRepo = new UserRepository();
    }

    public function index() {
        // Admin only (enforced by middleware)
        $users = $this->userRepo->findAll();
        Response::json($users);
    }

    public function show($id) {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            Response::json(['error' => 'User not found'], 404);
        }
        Response::json($user);
    }

    public function store() {
        $data = $_REQUEST['decoded_input'];
        
        // Basic validation
        if (empty($data['email']) || empty($data['password']) || empty($data['role'])) {
            Response::json(['error' => 'Email, password, and role are required'], 400);
        }

        if (!isset($data['tenant_id'])) {
            $data['tenant_id'] = 1;
        }

        try {
            $userId = $this->userRepo->create($data);
            if ($userId) {
                Response::json(['message' => 'User created', 'id' => $userId], 201);
            } else {
                Response::json(['error' => 'Failed to create user'], 500);
            }
        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 400);
        }
    }

    public function update($id) {
        $data = $_REQUEST['decoded_input'];
        if ($this->userRepo->update($id, $data)) {
            Response::json(['message' => 'User updated']);
        } else {
            Response::json(['error' => 'Failed to update user or no changes made'], 400);
        }
    }

    public function delete($id) {
        if ($this->userRepo->delete($id)) {
            Response::json(['message' => 'User deleted']);
        } else {
            Response::json(['error' => 'Failed to delete user'], 500);
        }
    }

    // Profile APIs
    public function getProfile() {
        $currentUser = $_REQUEST['user']; // From AuthMiddleware
        $user = $this->userRepo->findById($currentUser['sub']);
        
        if (!$user) {
            Response::json(['error' => 'Profile not found'], 404);
        }
        
        Response::json($user);
    }

    public function updateProfile() {
        $currentUser = $_REQUEST['user'];
        $data = $_REQUEST['decoded_input'];
        
        // Prevent changing role or tenant via profile update
        unset($data['role']);
        unset($data['tenant_id']);
        
        if ($this->userRepo->update($currentUser['sub'], $data)) {
            Response::json(['message' => 'Profile updated']);
        } else {
            Response::json(['error' => 'Failed to update profile or no changes made'], 400);
        }
    }
}
