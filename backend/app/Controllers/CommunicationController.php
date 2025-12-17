<?php
require_once "../Services/CommunicationService.php";
require_once "../Helpers/Response.php";

class CommunicationController {

  public static function index() {
    $query = $_SERVER['QUERY_STRING'] ?? "";
    $data = CommunicationService::getAll($query);
    Response::success($data);
  }

  public static function store() {
    $payload = $_REQUEST['payload'];
    $created = CommunicationService::create($payload);
    Response::success($created);
  }

  public static function update($id) {
    $payload = $_REQUEST['payload'];
    $updated = CommunicationService::update($id, $payload);
    Response::success($updated);
  }

  public static function delete($id) {
    CommunicationService::delete($id);
    Response::success(["deleted" => true]);
  }
}
