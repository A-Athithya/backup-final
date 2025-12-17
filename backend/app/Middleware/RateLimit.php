<?php
class RateLimit {
    public static function handle() {
        $_SESSION['hits'] = ($_SESSION['hits'] ?? 0) + 1;
        if ($_SESSION['hits'] > 100) {
            Response::error("Too many requests", 429);
        }
    }
}
