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
    public function getAllStaff($role) {
        $role = $this->normalizeRole($role);
        return $this->repo->getAll($role);
    }

    // ================= GET SINGLE STAFF =================
    public function getStaffById($role, $id) {
        $role = $this->normalizeRole($role);
        return $this->repo->getById($role, $id);
    }

    // ================= CREATE STAFF =================
    public function createStaff($data) {
        $role = $this->normalizeRole($data['role'] ?? '');

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
        $role = $this->normalizeRole($role);
        return $this->repo->update($role, $id, $data);
    }

    // ================= DELETE STAFF =================
    public function deleteStaff($role, $id) {
        $role = $this->normalizeRole($role);
        return $this->repo->delete($role, $id);
    }
}
