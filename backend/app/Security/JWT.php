<?php
class JWT {
    public static function generate($payload, $exp = 900) {
        $payload['exp'] = time() + $exp;
        return base64_encode(json_encode($payload));
    }

    public static function verify($token) {
        $data = json_decode(base64_decode($token), true);
        return ($data && $data['exp'] > time()) ? $data : null;
    }
}
