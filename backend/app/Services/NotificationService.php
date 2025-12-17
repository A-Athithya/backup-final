<?php
require_once "../Repositories/NotificationRepository.php";

class NotificationService {

  public static function getAll() {
    return NotificationRepository::all();
  }

  public static function create($data) {
    return NotificationRepository::create($data);
  }

  public static function markRead($id, $data) {
    return NotificationRepository::update($id, $data);
  }
}
