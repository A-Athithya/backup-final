<?php
require_once __DIR__ . '/../Helpers/Response.php';

<?php
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Repositories/PrescriptionRepository.php';

class PrescriptionController {
    private $repo;

    public function __construct() {
        $this->repo = new PrescriptionRepository();
    }

    public function index() {
        try {
            $data = $this->repo->getAll();
            Response::json($data);
        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 500);
        }
    }

    public function store() {
        $data = $_REQUEST['decoded_input'];
        try {
            $id = $this->repo->create($data);
            Response::json(['message' => 'Prescription created', 'id' => $id], 201);
        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 400);
        }
    }
}
