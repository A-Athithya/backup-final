<?php
require_once __DIR__ . '/../Repositories/PatientRepository.php';
require_once __DIR__ . '/EncryptionService.php';

class PatientService {
    private $repo;
    private $encryption;

    public function __construct() {
        $this->repo = new PatientRepository();
        $this->encryption = new EncryptionService();
    }

    public function getAllPatients() {
        $tenantId = $_REQUEST['user']['tenant_id'] ?? 1;
        $patients = $this->repo->getAll($tenantId);
        
        return array_map([$this, 'decryptPatientData'], $patients);
    }

    public function getPatientById($id) {
        $tenantId = $_REQUEST['user']['tenant_id'] ?? 1;
        $patient = $this->repo->getById($id, $tenantId);
        return $patient ? $this->decryptPatientData($patient) : null;
    }

    public function createPatient($data) {
        $data['tenant_id'] = $_REQUEST['user']['tenant_id'] ?? $data['tenant_id'] ?? 1;
        $data = $this->encryptPatientData($data);
        return $this->repo->create($data);
    }

    public function updatePatient($id, $data) {
        $tenantId = $_REQUEST['user']['tenant_id'] ?? 1;
        $data = $this->encryptPatientData($data);
        return $this->repo->update($id, $data, $tenantId);
    }

    public function deletePatient($id) {
        $tenantId = $_REQUEST['user']['tenant_id'] ?? 1;
        return $this->repo->softDelete($id, $tenantId);
    }

    public function getPatientAppointments($id) {
        $tenantId = $_REQUEST['user']['tenant_id'] ?? 1;
        return $this->repo->getAppointments($id, $tenantId);
    }

    private function encryptPatientData($data) {
        // Handle both camelCase and snake_case inputs
        $historyKey = isset($data['medicalHistory']) ? 'medicalHistory' : (isset($data['medical_history']) ? 'medical_history' : null);
        $allergiesKey = isset($data['allergies']) ? 'allergies' : null;

        if ($historyKey && !empty($data[$historyKey])) {
            $data[$historyKey] = $this->encryption->encrypt($data[$historyKey]);
        }
        if ($allergiesKey && !empty($data[$allergiesKey])) {
            $data[$allergiesKey] = $this->encryption->encrypt($data[$allergiesKey]);
        }
        return $data;
    }
    
    public function encryptData($data) {
        return $this->encryption->encrypt($data);
    }

    private function decryptPatientData($patient) {
        // Handle database column names which might be different from repository mapping
        $historyKey = isset($patient['medical_history']) ? 'medical_history' : (isset($patient['medicalHistory']) ? 'medicalHistory' : null);
        $allergiesKey = isset($patient['allergies']) ? 'allergies' : null;

        if ($historyKey && !empty($patient[$historyKey])) {
            $patient[$historyKey] = $this->encryption->decrypt($patient[$historyKey]);
        }
        if ($allergiesKey && !empty($patient[$allergiesKey])) {
            $patient[$allergiesKey] = $this->encryption->decrypt($patient[$allergiesKey]);
        }
        return $patient;
    }
}
