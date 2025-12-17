<?php
require_once __DIR__ . '/../Repositories/PatientRepository.php';

class PatientService {
    private $repo;

    public function __construct() {
        $this->repo = new PatientRepository();
    }

    public function getAllPatients() {
        return $this->repo->getAll();
    }

    public function getPatientById($id) {
        return $this->repo->getById($id);
    }

    public function createPatient($data) {
        // Validation logic can go here
        return $this->repo->create($data);
    }

    public function updatePatient($id, $data) {
        return $this->repo->update($id, $data);
    }

    public function deletePatient($id) {
        return $this->repo->delete($id);
    }
}
