<?php
require_once __DIR__ . '/../Repositories/AppointmentRepository.php';

class AppointmentService {
    private $repo;

    public function __construct() {
        $this->repo = new AppointmentRepository();
    }

    public function getAllAppointments() {
        return $this->repo->getAll();
    }

    public function createAppointment($data) {
        return $this->repo->create($data);
    }
}
