<?php

// Đảm bảo đường dẫn cho Laravel bootstrap trên Vercel

// Kiểm tra xem đang ở trên Vercel hay không
$isVercel = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL_REGION']);

// Sửa REQUEST_URI cho Vercel
if ($isVercel) {
    $_SERVER['SCRIPT_NAME'] = '/api/index.php';
}

// Đảm bảo header Content-Type được thiết lập đúng
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

// Load composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Đảm bảo có .env file
if (!file_exists(__DIR__ . '/../.env')) {
    copy(__DIR__ . '/../.env.example', __DIR__ . '/../.env');
}

// Khởi tạo application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Chạy application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response); 