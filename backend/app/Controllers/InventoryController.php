<?php
require_once "../Services/InventoryService.php";
require_once "../Helpers/Response.php";

class InventoryController {

  public static function index() {
    $data = InventoryService::getAll();
    Response::success($data);
  }

  public static function store() {
    $payload = $_REQUEST['payload'];
    $created = InventoryService::create($payload);
    Response::success($created);
  }

  public static function update($id) {
    $payload = $_REQUEST['payload'];
    $updated = InventoryService::update($id, $payload);
    Response::success($updated);
  }

  public static function delete($id) {
    InventoryService::delete($id);
    Response::success(["deleted" => true]);
  }
}
