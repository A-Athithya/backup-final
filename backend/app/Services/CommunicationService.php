<?php
require_once "../Repositories/CommunicationRepository.php";

class CommunicationService {

  public static function getAll($query = "") {
    return CommunicationRepository::all($query);
  }

  public static function create($data) {
    return CommunicationRepository::create($data);
  }

  public static function update($id, $data) {
    return CommunicationRepository::update($id, $data);
  }

  public static function delete($id) {
    CommunicationRepository::delete($id);
  }
}
