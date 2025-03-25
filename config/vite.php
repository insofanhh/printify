<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Manifest Path
    |--------------------------------------------------------------------------
    |
    | Đường dẫn đến file manifest.json của Vite.
    | Mặc định là public/build/manifest.json, nhưng có thể thay đổi tùy vào cấu hình.
    |
    */
    'manifest_path' => env('VITE_MANIFEST_PATH', public_path('dist/.vite/manifest.json')),

    /*
    |--------------------------------------------------------------------------
    | Entrypoints
    |--------------------------------------------------------------------------
    |
    | Các entrypoints mặc định nếu không được chỉ định.
    |
    */
    'entrypoints' => [
        'resources/css/app.css',
        'resources/js/app.js',
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Server
    |--------------------------------------------------------------------------
    |
    | Cấu hình máy chủ phát triển Vite.
    |
    */
    'dev_server' => [
        'enabled' => env('VITE_DEV_SERVER_ENABLED', false),
        'url' => env('VITE_DEV_SERVER_URL', 'http://localhost:5173'),
    ],
]; 