<?php
require_once __DIR__ . '/BaseRepository.php';

class UserRepository extends BaseRepository {
    public function __construct() {
        parent::__construct();
    }

    public function create($data) {
        $sql = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $stmt = $this->db->prepare($sql);
        
        // Hash password
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $data['role']); // e.g. 'admin', 'doctor', 'patient'
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug
        // Debug removed
        
        return $user;
    }
}
