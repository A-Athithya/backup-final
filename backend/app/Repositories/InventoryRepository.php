<?php
require_once "../Config/database.php";

class InventoryRepository {

  public static function all() {
    global $pdo;
    return $pdo->query("SELECT * FROM medicines")->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function create($d) {
    global $pdo;
    $stmt = $pdo->prepare("
      INSERT INTO medicines (name, stock, price, status)
      VALUES (?,?,?,?)
    ");
    $stmt->execute([
      $d['name'],
      $d['stock'],
      $d['price'],
      $d['status']
    ]);
    return self::find($pdo->lastInsertId());
  }

  public static function update($id, $d) {
    global $pdo;
    $stmt = $pdo->prepare("
      UPDATE medicines SET name=?, stock=?, price=?, status=? WHERE id=?
    ");
    $stmt->execute([
      $d['name'], $d['stock'], $d['price'], $d['status'], $id
    ]);
    return self::find($id);
  }

  public static function delete($id) {
    global $pdo;
    $pdo->prepare("DELETE FROM medicines WHERE id=?")->execute([$id]);
  }

  public static function find($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM medicines WHERE id=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}
