<?php
require_once __DIR__ . '/../Helpers/Response.php';

class UserController {
    public function index() {
        Response::json(['message' => 'List of users']);
    }

    public function store() {
        Response::json(['message' => 'User created']);
    }
}
