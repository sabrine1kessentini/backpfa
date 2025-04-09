<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // Autorise toutes les origines
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,

    // Nouvelle option ajoutée pour les headers personnalisés
    'allowed_headers_list' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'Accept',
    ],
];