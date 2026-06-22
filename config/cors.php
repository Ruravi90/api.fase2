<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:4200'),
        'https://fase2spa.com',
        'https://www.fase2spa.com',
        'https://sistema.fase2spa.com',
        'https://ui.fase2spa.com',
        'https://sistema.fase2spa.com.mx',
        'https://ui.fase2spa.com.mx',
        'http://localhost:4200',
        'http://127.0.0.1:4200',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    /*
    | CRITICAL: must be true for cookie-based auth to work across subdomains.
    | This allows the browser to send cookies (HttpOnly) with cross-origin requests.
    */
    'supports_credentials' => true,

];
