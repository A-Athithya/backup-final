<?php
require_once "../Services/AuthService.php";
require_once "../Helpers/Response.php";

class AuthController {
  public static function login() {
    $payload = $_REQUEST['payload'];
    $result = AuthService::login($payload);
    Response::success($result);
  }
}
