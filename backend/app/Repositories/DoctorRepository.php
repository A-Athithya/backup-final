<?php
require_once __DIR__ . '/BaseRepository.php';

class DoctorRepository extends BaseRepository {
    protected $table = 'doctors';

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
        (name, email, gender, age, specialization, qualification, experience, contact,
         address, available_days, available_time, department, license_number,
         rating, consultation_fee, bio, status)
        VALUES
        (:name, :email, :gender, :age, :specialization, :qualification, :experience, :contact,
         :address, :available_days, :available_time, :department, :license_number,
         :rating, :consultation_fee, :bio, :status)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET
            name=:name, email=:email, gender=:gender, age=:age,
            specialization=:specialization, qualification=:qualification,
            experience=:experience, contact=:contact, address=:address,
            available_days=:available_days, available_time=:available_time,
            department=:department, license_number=:license_number,
            rating=:rating, consultation_fee=:consultation_fee,
            bio=:bio, status=:status
            WHERE id=:id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
