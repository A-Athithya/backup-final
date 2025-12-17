<?php


class Response {
    public static function json($data, $status = 200) {
        http_response_code($status);
        
        // Plaintext Response
        $json = json_encode($data);
        
        // Clean any accidental output (whitespace/BOM)
        if (ob_get_length()) ob_clean();
        
        echo $json;
        exit;
    }
}
