<?php
require_once __DIR__ . '/../Services/DoctorService.php';
require_once __DIR__ . '/../Helpers/Response.php';

class DoctorController {
    private $service;

    public function __construct() {
        $this->service = new DoctorService();
    }

    public function index() {
        Response::json($this->service->getAllDoctors());
    }

    public function show($id) {
        $doctor = $this->service->find($id);
        if (!$doctor) {
            Response::json(['message' => 'Doctor not found'], 404);
        }
        Response::json($doctor);
    }

    public function store() {
        $data = $_REQUEST['decoded_input'];
        $this->service->createDoctor($data);
        Response::json(['message' => 'Doctor created successfully']);
    }

    public function update($id) {
        $data = $_REQUEST['decoded_input'];
        $this->service->update($id, $data);
        Response::json(['message' => 'Doctor updated successfully']);
    }

    public function delete($id) {
        $this->service->delete($id);
        Response::json(['message' => 'Doctor deleted successfully']);
    }
}
