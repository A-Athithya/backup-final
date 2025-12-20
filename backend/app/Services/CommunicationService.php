<?php
require_once __DIR__ . '/../Repositories/CommunicationRepository.php';
require_once __DIR__ . '/EncryptionService.php';

class CommunicationService {
    private $repo;
    private $encryption;

    public function __construct() {
        $this->repo = new CommunicationRepository();
        $this->encryption = new EncryptionService();
    }

    public function addNote($data) {
        $tenantId = $_REQUEST['user']['tenant_id'] ?? 1;
        $senderId = $_REQUEST['user']['id'];
        $senderRole = $_REQUEST['user']['role'];

        $encryptedContent = $this->encryption->encrypt($data['content']);

        $noteData = [
            'appointment_id' => $data['appointment_id'],
            'sender_id' => $senderId,
            'sender_role' => $senderRole,
            'content' => $encryptedContent,
            'tenant_id' => $tenantId
        ];

        return $this->repo->create($noteData);
    }

    public function getNotesByAppointment($appointmentId) {
        $tenantId = $_REQUEST['user']['tenant_id'] ?? 1;
        $notes = $this->repo->getByAppointmentId($appointmentId, $tenantId);
        
        return array_map(function($note) {
            $note['content'] = $this->encryption->decrypt($note['content']);
            return $note;
        }, $notes);
    }

    public function getMessageHistory() {
        $tenantId = $_REQUEST['user']['tenant_id'] ?? 1;
        $messages = $this->repo->getMessageHistory($tenantId);

        return array_map(function($message) {
            $message['content'] = $this->encryption->decrypt($message['content']);
            return $message;
        }, $messages);
    }
}
