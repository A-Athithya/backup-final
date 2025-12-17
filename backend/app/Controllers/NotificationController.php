<?php
require_once "../Services/NotificationService.php";
require_once "../Helpers/Response.php";

class NotificationController {

  public static function index() {
    $data = NotificationService::getAll();
    Response::success($data);
  }

  public static function store() {
    $payload = $_REQUEST['payload'];
    $created = NotificationService::create($payload);
    Response::success($created);
  }

  public static function update($id) {
    $payload = $_REQUEST['payload'];
    $updated = NotificationService::markRead($id, $payload);
    Response::success($updated);
  }
}
