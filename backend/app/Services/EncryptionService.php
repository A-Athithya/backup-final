<?php

class EncryptionService {
    private $key;

    public function __construct() {
        $config = require __DIR__ . '/../Config/config.php';
        $this->key = $config['security']['aes_key'];
    }

    public function encrypt($data) {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        // Use OPENSSL_RAW_DATA (1) to get binary ciphertext
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $this->key, OPENSSL_RAW_DATA, $iv);
        
        // Return IV + Encrypted Data (Base64 encoded)
        return base64_encode($iv . $encrypted);
    }

    public function decrypt($data) {
        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        if (strlen($iv) !== $ivLength) {
            return null; // Invalid IV
        }

        // Use OPENSSL_RAW_DATA (1) because $encrypted is binary
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $this->key, OPENSSL_RAW_DATA, $iv);
        
        // Try to decode JSON
        $json = json_decode($decrypted, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $json : $decrypted;
    }
}
