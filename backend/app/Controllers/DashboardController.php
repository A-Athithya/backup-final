<?php
require_once __DIR__ . '/../Helpers/Response.php';

class DashboardController {
    public function index() {
        require_once __DIR__ . '/../Services/PatientService.php';
        require_once __DIR__ . '/../Services/AppointmentService.php';
        require_once __DIR__ . '/../Services/StaffService.php';

        $patientService = new PatientService();
        $appointmentService = new AppointmentService();
        $staffService = new StaffService();

        $patients = $patientService->getAllPatients();
        $appointments = $appointmentService->getAllAppointments();
        
        // Doctors are staff with role 'doctor'. 
        // If getAllStaff returns all, we can filter here or just return all as 'doctors' for now
        // Assuming StaffService has getAll()
        $staff = $staffService->getAllStaff(); 
        $doctors = array_filter($staff, function($s) {
            return isset($s['role']) && strtolower($s['role']) === 'doctor';
        });
        
        Response::json([
            'patients' => $patients,
            'appointments' => $appointments,
            'doctors' => array_values($doctors),
            'medicines' => [], // Placeholder to prevent crash
            'invoices' => []   // Placeholder to prevent crash
        ]);
    }
}
