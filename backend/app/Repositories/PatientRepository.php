<?php
require_once 'BaseRepository.php';

class PatientRepository extends BaseRepository {

    public function getAll() {
        $stmt = $this->db->prepare("
            SELECT id, name, email, phone as contact, gender, age, blood_group as bloodGroup, 
                   address, registered_date as registeredDate, medical_history as medicalHistory, 
                   allergies, emergency_contact as emergencyContact, status 
            FROM patients 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM patients WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO patients (name, email, phone, gender, age, blood_group, address, registered_date, medical_history, allergies, emergency_contact, status) 
                VALUES (:name, :email, :phone, :gender, :age, :blood_group, :address, :registered_date, :medical_history, :allergies, :emergency_contact, :status)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':phone' => $data['contact'] ?? null,
            ':gender' => $data['gender'] ?? null,
            ':age' => $data['age'] ?? null,
            ':blood_group' => $data['bloodGroup'] ?? null,
            ':address' => $data['address'] ?? null,
            ':registered_date' => $data['registeredDate'] ?? date('Y-m-d'),
            ':medical_history' => $data['medicalHistory'] ?? null,
            ':allergies' => $data['allergies'] ?? null,
            ':emergency_contact' => $data['emergencyContact'] ?? null,
            ':status' => $data['status'] ?? 'Active'
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE patients SET 
                    name = :name,
                    email = :email,
                    phone = :phone,
                    gender = :gender,
                    age = :age,
                    blood_group = :blood_group,
                    address = :address,
                    registered_date = :registered_date,
                    medical_history = :medical_history,
                    allergies = :allergies,
                    emergency_contact = :emergency_contact,
                    status = :status
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':phone' => $data['contact'] ?? null,
            ':gender' => $data['gender'] ?? null,
            ':age' => $data['age'] ?? null,
            ':blood_group' => $data['bloodGroup'] ?? null,
            ':address' => $data['address'] ?? null,
            ':registered_date' => $data['registeredDate'] ?? date('Y-m-d'),
            ':medical_history' => $data['medicalHistory'] ?? null,
            ':allergies' => $data['allergies'] ?? null,
            ':emergency_contact' => $data['emergencyContact'] ?? null,
            ':status' => $data['status'] ?? 'Active',
            ':id' => $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM patients WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
