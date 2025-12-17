<?php
require_once "../Repositories/InventoryRepository.php";

class InventoryService {

  public static function getAll() {
    return InventoryRepository::all();
  }

  public static function create($data) {
    return InventoryRepository::create($data);
  }

  public static function update($id, $data) {
    return InventoryRepository::update($id, $data);
  }

  public static function delete($id) {
    InventoryRepository::delete($id);
  }
}
