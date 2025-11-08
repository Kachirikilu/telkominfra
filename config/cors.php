<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines which domains are allowed to access your
    | application via AJAX HTTP requests.
    |
    | For more information: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'build/assets/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        // Untuk Dev Tunnels:
        // Ganti URL di bawah dengan alamat Dev Tunnels Anda yang sebenarnya,
        // misalnya: 'https://4m445w5p-8000.asse.devtunnels.ms'
        'https://*.devtunnels.ms', 
        
        // Simpan '*' ini jika Anda ingin mengizinkan akses dari mana saja
        // (Hanya untuk pengembangan, sangat tidak disarankan di produksi)
        '*', 
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];