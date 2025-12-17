<?php
class Validator {
    public static function required($data, $fields) {
        foreach ($fields as $f) {
            if (!isset($data[$f])) Response::error("$f required");
        }
    }
}
