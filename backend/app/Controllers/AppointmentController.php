<?php
require_once __DIR__ . '/../Helpers/Response.php';

class AppointmentController {
    private $service;

    public function __construct() {
        require_once __DIR__ . '/../Services/AppointmentService.php';
        $this->service = new AppointmentService();
    }

    // ✅ Get all appointments
    public function index() {
        $data = $this->service->getAllAppointments();
        Response::json($data);
    }

    // ✅ Get single appointment
    public function show($id) {
        try {
            $appointment = $this->service->getAppointmentById($id);
            if (!$appointment) {
                Response::json(['error' => 'Appointment not found'], 404);
                return;
            }
            Response::json($appointment);
        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 400);
        }
    }

    // ✅ Create new appointment
    public function store() {
        $data = $_REQUEST['decoded_input'];
        try {
            $id = $this->service->createAppointment($data);
            Response::json(['message' => 'Appointment created', 'id' => $id], 201);
        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 400);
        }
    }

    // ✅ Update appointment
    public function update($id) {
        $data = $_REQUEST['decoded_input'];
        try {
            $this->service->updateAppointment($id, $data);
            Response::json(['message' => 'Appointment updated', 'id' => $id]);
        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 400);
        }
    }

    // ✅ Delete appointment
    public function delete($id) {
        try {
            $this->service->deleteAppointment($id);
            Response::json(['message' => 'Appointment deleted', 'id' => $id]);
        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 400);
        }
    }
}
