<?php
require_once __DIR__ . '/../Repositories/StaffRepository.php';

class StaffService {

    private $repo;

    public function __construct() {
        $this->repo = new StaffRepository();
    }

    // ================= ROLE NORMALIZATION =================
    // Frontend: nurses → Backend: nurse
    private function normalizeRole($role) {
        $role = strtolower($role);
        $map = [
            'doctors' => 'doctor',
            'nurses' => 'nurse',
            'pharmacists' => 'pharmacist',
            'receptionists' => 'receptionist',
            'admins' => 'admin',
        ];
        return $map[$role] ?? $role;
    }

    // ================= GET ALL STAFF =================
    public function getAllStaff($role = 'doctor') {
        $tenantId = $_REQUEST['user']['tenant_id'] ?? 1;
        $role = $this->normalizeRole($role);
        return $this->repo->getAll($role, $tenantId);
    }

    // ================= GET SINGLE STAFF =================
    public function getStaffById($role, $id) {
        $tenantId = $_REQUEST['user']['tenant_id'] ?? 1;
        $role = $this->normalizeRole($role);
        return $this->repo->getById($role, $id, $tenantId);
    }

    // ================= CREATE STAFF =================
    public function createStaff($data) {
        $data['tenant_id'] = $_REQUEST['user']['tenant_id'] ?? $data['tenant_id'] ?? 1;
        $role = $this->normalizeRole($data['role'] ?? 'doctor');

        // ✅ Create User account for login if password provided
        if (isset($data['password']) && !empty($data['password'])) {
            require_once __DIR__ . '/../Repositories/UserRepository.php';
            $userRepo = new UserRepository();
            
            // Link existing user or create new one
            $existing = $userRepo->findByEmail($data['email']);
            if (!$existing) {
                $userRepo->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $data['password'], // UserRepo hashes it
                    'role' => $role,
                    'tenant_id' => $data['tenant_id']
                ]);
            }
        }

        switch ($role) {
            case 'doctor':
                return $this->repo->createDoctor($data);

            case 'nurse':
                return $this->repo->createNurse($data);

            case 'pharmacist':
                return $this->repo->createPharmacist($data);

            case 'receptionist':
                return $this->repo->createReceptionist($data);

            default:
                throw new Exception('Invalid staff role');
        }
    }

    // ================= UPDATE STAFF =================
    public function updateStaff($role, $id, $data) {
        $tenantId = $_REQUEST['user']['tenant_id'] ?? 1;
        $role = $this->normalizeRole($role);
        return $this->repo->update($role, $id, $tenantId, $data);
    }

    // ================= DELETE STAFF =================
    public function deleteStaff($role, $id) {
        $tenantId = $_REQUEST['user']['tenant_id'] ?? 1;
        $role = $this->normalizeRole($role);
        return $this->repo->delete($role, $id, $tenantId);
    }
}
