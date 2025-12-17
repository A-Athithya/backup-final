<?php
return [
    'app_name' => 'Hcare API',
    'base_url' => 'http://localhost/Hcare%20php%20int/backend/public',
    'security' => [
        'aes_key' => getenv('AES_KEY'),
        'jwt_secret' => getenv('JWT_SECRET'),
        'jwt_expiry' => getenv('JWT_EXPIRY'),
        'refresh_token_expire' => getenv('REFRESH_TOKEN_EXPIRY')
    ]
];
