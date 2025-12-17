<?php
require_once "../Security/AES.php";

class Response {
  public static function success($data) {
    echo json_encode([
      "data" => AES::encrypt($data)
    ]);
  }
}
