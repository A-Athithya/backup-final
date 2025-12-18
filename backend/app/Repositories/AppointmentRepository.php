<?php
require_once 'BaseRepository.php';

class AppointmentRepository extends BaseRepository {
    public function getAll($tenantId) {
        $stmt = $this->db->prepare("SELECT * FROM appointments WHERE tenant_id = :tenant_id ORDER BY appointment_date DESC");
        try {
            $stmt->execute([':tenant_id' => $tenantId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function create($data) {
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason, status, payment_amount, notes, tenant_id) 
                VALUES (:patient_id, :doctor_id, :appointment_date, :appointment_time, :reason, :status, :payment_amount, :notes, :tenant_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':patient_id' => $data['patientId'],
            ':doctor_id' => $data['doctorId'],
            ':appointment_date' => $data['appointmentDate'],
            ':appointment_time' => $data['appointmentTime'],
            ':reason' => $data['reason'] ?? null,
            ':status' => $data['status'] ?? 'Pending',
            ':payment_amount' => $data['paymentAmount'] ?? 0,
            ':notes' => $data['notes'] ?? null,
            ':tenant_id' => $data['tenant_id']
        ]);
        return $this->db->lastInsertId();
    }
}
