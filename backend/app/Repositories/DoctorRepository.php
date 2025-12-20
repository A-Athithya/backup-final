<?php
require_once __DIR__ . '/BaseRepository.php';

class DoctorRepository extends BaseRepository {
    protected $table = 'doctors';

    public function getAll($tenantId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE tenant_id = :tenant_id ORDER BY id DESC");
        $stmt->execute([':tenant_id' => $tenantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id, $tenantId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $tenantId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
        (name, email, gender, age, specialization, qualification, experience, contact,
         address, available_days, available_time, department, license_number,
         rating, consultation_fee, bio, status, tenant_id)
        VALUES
        (:name, :email, :gender, :age, :specialization, :qualification, :experience, :contact,
         :address, :available_days, :available_time, :department, :license_number,
         :rating, :consultation_fee, :bio, :status, :tenant_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data, $tenantId) {
        $sql = "UPDATE {$this->table} SET
            name=:name, email=:email, gender=:gender, age=:age,
            specialization=:specialization, qualification=:qualification,
            experience=:experience, contact=:contact, address=:address,
            available_days=:available_days, available_time=:available_time,
            department=:department, license_number=:license_number,
            rating=:rating, consultation_fee=:consultation_fee,
            bio=:bio, status=:status
            WHERE id=:id AND tenant_id=:tenant_id";
        $data['id'] = $id;
        $data['tenant_id'] = $tenantId;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id, $tenantId) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ? AND tenant_id = ?");
        return $stmt->execute([$id, $tenantId]);
    }

    public function findByEmail($email, $tenantId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email AND tenant_id = :tenant_id LIMIT 1");
        $stmt->execute([':email' => $email, ':tenant_id' => $tenantId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
