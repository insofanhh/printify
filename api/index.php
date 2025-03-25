<?php

// Load composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Lấy đường dẫn thực đến thư mục public
$publicPath = __DIR__ . '/../public';

// Khởi tạo application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Chạy application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response); 