<?php
require_once __DIR__ . '/../Repositories/AppointmentRepository.php';

class AppointmentService {
    private $repo;

    public function __construct() {
        $this->repo = new AppointmentRepository();
    }

    public function getAllAppointments() {
        $tenantId = $_REQUEST['user']['tenant_id'] ?? 1;
        return $this->repo->getAll($tenantId);
    }

    public function createAppointment($data) {
        $data['tenant_id'] = $_REQUEST['user']['tenant_id'] ?? 1;
        return $this->repo->create($data);
    }
}
