<?php
require_once "../Services/PatientService.php";
require_once "../Helpers/Response.php";

class PatientController {

  public static function index() {
    $data = PatientService::getAll();
    Response::success($data);
  }

  public static function store() {
    $payload = $_REQUEST['payload'];
    $patient = PatientService::create($payload);
    Response::success($patient);
  }

  public static function update($id) {
    $payload = $_REQUEST['payload'];
    $patient = PatientService::update($id, $payload);
    Response::success($patient);
  }

  public static function delete($id) {
    PatientService::delete($id);
    Response::success(["deleted" => true]);
  }
}
