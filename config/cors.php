<?php

return [

    'paths' => ['api/*', 'ep/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://vangoo.mx',
        'https://www.vangoo.mx',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['X-CSRF-TOKEN'],

    'max_age' => 0,

    'supports_credentials' => true,

];
