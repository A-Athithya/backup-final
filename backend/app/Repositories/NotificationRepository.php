<?php
require_once "../Config/database.php";

class NotificationRepository {

  public static function all() {
    global $pdo;
    return $pdo->query("SELECT * FROM notifications ORDER BY timestamp DESC")
               ->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function create($d) {
    global $pdo;
    $stmt = $pdo->prepare("
      INSERT INTO notifications (roles, userId, message, redirect, timestamp, readBy)
      VALUES (?,?,?,?,?,?)
    ");
    $stmt->execute([
      json_encode($d['roles']),
      $d['userId'] ?? null,
      $d['message'],
      $d['redirect'],
      $d['timestamp'],
      json_encode([])
    ]);
    return self::find($pdo->lastInsertId());
  }

  public static function update($id, $d) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE notifications SET readBy=? WHERE id=?");
    $stmt->execute([ json_encode($d['readBy']), $id ]);
    return self::find($id);
  }

  public static function find($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE id=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}
