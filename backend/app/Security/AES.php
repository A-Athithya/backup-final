<?php
class AES {
    public static function encrypt($data) {
        return openssl_encrypt(json_encode($data), "AES-256-CBC", AES_KEY, 0, substr(AES_KEY,0,16));
    }

    public static function decrypt($cipher) {
        return json_decode(openssl_decrypt($cipher, "AES-256-CBC", AES_KEY, 0, substr(AES_KEY,0,16)), true);
    }
}
