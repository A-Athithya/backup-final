<?php
require_once 'BaseRepository.php';

class AppointmentRepository extends BaseRepository {
    public function getAll() {
        // Return structured array matching frontend expectations
        // Frontend expects: id, patientId, doctorId, appointmentDate, appointmentTime, status, paymentAmount
        $stmt = $this->db->prepare("SELECT * FROM appointments ORDER BY appointment_date DESC");
        // If table doesn't exist, this might fail. But we assume DB exists. 
        // If execute fails, return empty
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function create($data) {
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason, status, payment_amount, notes) 
                VALUES (:patient_id, :doctor_id, :appointment_date, :appointment_time, :reason, :status, :payment_amount, :notes)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':patient_id' => $data['patientId'],
            ':doctor_id' => $data['doctorId'],
            ':appointment_date' => $data['appointmentDate'],
            ':appointment_time' => $data['appointmentTime'],
            ':reason' => $data['reason'] ?? null,
            ':status' => $data['status'] ?? 'Pending',
            ':payment_amount' => $data['paymentAmount'] ?? 0,
            ':notes' => $data['notes'] ?? null
        ]);
        return $this->db->lastInsertId();
    }
}
