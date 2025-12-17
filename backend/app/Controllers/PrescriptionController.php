<?php
require_once __DIR__ . '/../Helpers/Response.php';

class PrescriptionController {
    public function index() {
        Response::json(['message' => 'List of prescriptions']);
    }

    public function store() {
        Response::json(['message' => 'Prescription created']);
    }
}
