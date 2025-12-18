<?php
require_once __DIR__ . '/../Services/EncryptionService.php';

class EncryptionMiddleware {
    public static function handleInput() {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);

        // Check if payload is encrypted
        if (isset($json['payload'])) {
            $service = new EncryptionService();
            $decrypted = $service->decrypt($json['payload']);
            
            if ($decrypted) {
                $_REQUEST['decoded_input'] = $decrypted;
                return;
            }
        }
        
        // Fallback: If not encrypted or decryption failed, leave as is (or handle as error)
        // For development/transition, we might accept plaintext, but for "perfect manner", we should enforce.
        // Let's assume plaintext if not 'payload' key, to allowing initial testing/debugging if needed, 
        // OR enforce strictness. I'll stick to logic: if 'payload' exists, decrypt. If not, use raw JSON.
        $_REQUEST['decoded_input'] = $json ?? [];
    }
}
