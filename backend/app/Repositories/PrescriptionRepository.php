<?php
require_once __DIR__ . '/BaseRepository.php';

class PrescriptionRepository extends BaseRepository {
    public function __construct() {
        parent::__construct();
    }

    public function getAll() {
        // Fetch all prescriptions ordered by latest
        $stmt = $this->db->prepare("
            SELECT id, patient_id as patientId, doctor_id as doctorId, 
                   date as prescribedDate, medicines, 
                   dosage, instructions, status
            FROM prescriptions 
            ORDER BY id DESC
        ");
        $stmt->execute();
        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode JSON fields
        foreach ($prescriptions as &$p) {
            $p['medicines'] = json_decode($p['medicines'], true) ?? [];
            // Add dummy data for UI fields not in DB or map them
            $p['nextFollowUp'] = 'N/A'; // Example default, can be added to DB later
            $p['notes'] = $p['instructions']; // Map instructions to notes
        }

        return $prescriptions;
    }

    public function create($data) {
        $sql = "INSERT INTO prescriptions (patient_id, doctor_id, medicines, dosage, instructions, date, status) 
                VALUES (:patient_id, :doctor_id, :medicines, :dosage, :instructions, :date, :status)";
        
        $stmt = $this->db->prepare($sql);
        
        $medicinesJson = isset($data['medicines']) ? json_encode($data['medicines']) : '[]';
        
        $stmt->execute([
            ':patient_id' => $data['patientId'],
            ':doctor_id' => $data['doctorId'],
            ':medicines' => $medicinesJson,
            ':dosage' => $data['dosage'] ?? '',
            ':instructions' => $data['notes'] ?? $data['instructions'] ?? '',
            ':date' => $data['date'] ?? date('Y-m-d'),
            ':status' => $data['status'] ?? 'Active'
        ]);

        return $this->db->lastInsertId();
    }
}
