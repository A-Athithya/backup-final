<?php
require_once "../Config/database.php";

class CommunicationRepository {

  public static function all($query = "") {
    global $pdo;
    $sql = "SELECT * FROM communications";
    if ($query) $sql .= " WHERE " . str_replace("&", " AND ", $query);
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function create($d) {
    global $pdo;
    $stmt = $pdo->prepare("
      INSERT INTO communications
      (patientId, nurseId, doctorId, query, reply, status, timestamp)
      VALUES (?,?,?,?,?,?,?)
    ");
    $stmt->execute([
      $d['patientId'],
      $d['nurseId'],
      $d['doctorId'],
      $d['query'],
      $d['reply'] ?? "",
      $d['status'],
      $d['timestamp']
    ]);
    return self::find($pdo->lastInsertId());
  }

  public static function update($id, $d) {
    global $pdo;
    $stmt = $pdo->prepare("
      UPDATE communications SET reply=?, status=?, timestamp=?
      WHERE id=?
    ");
    $stmt->execute([
      $d['reply'],
      $d['status'],
      $d['timestamp'],
      $id
    ]);
    return self::find($id);
  }

  public static function delete($id) {
    global $pdo;
    $pdo->prepare("DELETE FROM communications WHERE id=?")->execute([$id]);
  }

  public static function find($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM communications WHERE id=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}
