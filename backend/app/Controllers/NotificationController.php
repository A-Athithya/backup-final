<?php
require_once __DIR__ . '/../Helpers/Response.php';

class NotificationController {
    public function index() {
        // Return dummy notifications to prevent 404
        Response::json([
            [
                'id' => 1,
                'message' => 'Welcome to the Healthcare Portal',
                'type' => 'info',
                'read' => false,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
