<?php
class Hash {
    public static function make($pwd) {
        return password_hash($pwd, PASSWORD_BCRYPT);
    }
    public static function check($pwd, $hash) {
        return password_verify($pwd, $hash);
    }
}
