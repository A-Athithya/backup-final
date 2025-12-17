<?php
require_once "../Config/database.php";

class PatientRepository {

  public static function all() {
    global $pdo;
    return $pdo->query("SELECT * FROM patients")->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function create($d) {
    global $pdo;
    $stmt = $pdo->prepare("
      INSERT INTO patients (name,age,gender,contact,email,address,status)
      VALUES (?,?,?,?,?,?,?)
    ");
    $stmt->execute([
      $d['name'],$d['age'],$d['gender'],$d['contact'],
      $d['email'],$d['address'],$d['status']
    ]);
    return self::find($pdo->lastInsertId());
  }

  public static function update($id, $d) {
    global $pdo;
    $stmt = $pdo->prepare("
      UPDATE patients SET name=?,age=?,gender=?,contact=?,email=?,address=?,status=?
      WHERE id=?
    ");
    $stmt->execute([
      $d['name'],$d['age'],$d['gender'],$d['contact'],
      $d['email'],$d['address'],$d['status'],$id
    ]);
    return self::find($id);
  }

  public static function delete($id) {
    global $pdo;
    $pdo->prepare("DELETE FROM patients WHERE id=?")->execute([$id]);
  }

  public static function find($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}
