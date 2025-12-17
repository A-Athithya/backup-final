<?php
require_once __DIR__ . '/../Repositories/DoctorRepository.php';

class DoctorService {
    private $repo;

    public function __construct() {
        $this->repo = new DoctorRepository();
    }

    public function all() {
        return $this->repo->getAll();
    }

    public function find($id) {
        return $this->repo->getById($id);
    }

    public function create($data) {
        return $this->repo->create($data);
    }

    public function update($id, $data) {
        return $this->repo->update($id, $data);
    }

    public function delete($id) {
        return $this->repo->delete($id);
    }
}
