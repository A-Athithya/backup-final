<?php
require_once __DIR__ . '/../Helpers/Response.php';

class BillingController {
    public function index() {
        Response::json(['message' => 'Billing data']);
    }
}
