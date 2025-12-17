<?php
require_once __DIR__ . '/../Repositories/InventoryRepository.php';

class InventoryService {
    private $repo;

    public function __construct() {
        $this->repo = new InventoryRepository();
    }

    public function getAllMedicines() {
        return $this->repo->getAll();
    }

    public function getMedicine($id) {
        return $this->repo->getById($id);
    }

    public function addMedicine($data) {
        return $this->repo->create($data);
    }

    public function updateMedicine($id, $data) {
        return $this->repo->update($id, $data);
    }

    public function deleteMedicine($id) {
        return $this->repo->delete($id);
    }
}
