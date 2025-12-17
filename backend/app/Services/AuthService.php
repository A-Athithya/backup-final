<?php
require_once "../Repositories/UserRepository.php";
require_once "../Security/Hash.php";
require_once "../Security/JWT.php";

class AuthService {

  public static function login($data) {
    $user = UserRepository::findByEmail($data['email']);

    if (!$user || !Hash::verify($data['password'], $user['password'])) {
      http_response_code(401);
      exit;
    }

    $token = JWT::generate([
      "id" => $user['id'],
      "name" => $user['name'],
      "role" => $user['role']
    ]);

    $_SESSION['access_token'] = $token;

    return [
      "user" => $user,
      "token" => $token
    ];
  }
}
