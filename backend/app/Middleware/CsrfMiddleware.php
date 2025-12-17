<?php
class CsrfMiddleware {
    public static function handle($token) {
        if (!CSRF::validate($token)) {
            Response::error("Invalid CSRF", 403);
        }
    }
}
