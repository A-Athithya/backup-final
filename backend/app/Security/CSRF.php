<?php
class CSRF {
    public static function generate() {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }

    public static function validate($token) {
        return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
    }
}
