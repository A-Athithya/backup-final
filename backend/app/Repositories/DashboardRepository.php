<?php
require_once __DIR__ . '/BaseRepository.php';

class DashboardRepository extends BaseRepository {
    public function __construct() {
        parent::__construct();
    }

    public function getPatientCount($tenantId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM patients 
            WHERE tenant_id = :tenant_id AND is_deleted = 0
        ");
        $stmt->execute([':tenant_id' => $tenantId]);
        return (int) $stmt->fetchColumn();
    }

    public function getAppointmentStats($tenantId) {
        $stmt = $this->db->prepare("
            SELECT status, COUNT(*) as count 
            FROM appointments 
            WHERE tenant_id = :tenant_id 
            GROUP BY status
        ");
        $stmt->execute([':tenant_id' => $tenantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPrescriptionStats($tenantId) {
        $stmt = $this->db->prepare("
            SELECT status, COUNT(*) as count 
            FROM prescriptions 
            WHERE tenant_id = :tenant_id AND is_deleted = 0
            GROUP BY status
        ");
        $stmt->execute([':tenant_id' => $tenantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTenantAnalytics() {
        $stmt = $this->db->prepare("
            SELECT t.name as tenant_name, 
                   (SELECT COUNT(*) FROM patients p WHERE p.tenant_id = t.id AND p.is_deleted = 0) as patient_count,
                   (SELECT COUNT(*) FROM appointments a WHERE a.tenant_id = t.id) as appointment_count,
                   (SELECT COUNT(*) FROM prescriptions pr WHERE pr.tenant_id = t.id AND pr.is_deleted = 0) as prescription_count
            FROM tenants t
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
