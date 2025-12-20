<?php
require_once __DIR__ . '/../Repositories/DashboardRepository.php';

class DashboardService {
    private $repo;

    public function __construct() {
        $this->repo = new DashboardRepository();
    }

    public function getDashboardStats($tenantId) {
        $patientCount = $this->repo->getPatientCount($tenantId);
        $appointmentStats = $this->repo->getAppointmentStats($tenantId);
        $prescriptionStats = $this->repo->getPrescriptionStats($tenantId);

        return [
            'total_patients' => $patientCount,
            'appointments' => $appointmentStats,
            'prescriptions' => $prescriptionStats,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    public function getTenantAnalytics() {
        return $this->repo->getTenantAnalytics();
    }
}
