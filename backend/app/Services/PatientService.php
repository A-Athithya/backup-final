<?php
require_once "../Repositories/PatientRepository.php";

class PatientService {

  public static function getAll() {
    return PatientRepository::all();
  }

  public static function create($data) {
    return PatientRepository::create($data);
  }

  public static function update($id, $data) {
    return PatientRepository::update($id, $data);
  }

  public static function delete($id) {
    return PatientRepository::delete($id);
  }
}
