<?php

// Thiết lập mặc định cho thông tin máy chủ
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../public';

// Thiết lập Content-Type mặc định
header('Content-Type: text/html; charset=UTF-8');

// Kiểm tra xem đang ở trên Vercel hay không
$isVercel = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL_REGION']);

// Sửa REQUEST_URI và SCRIPT_NAME cho Vercel
if ($isVercel) {
    $_SERVER['SCRIPT_NAME'] = '/api/index.php';
    
    // Chuyển hướng request đến /public 
    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    
    // Nếu request là file tĩnh trong public, trả về file đó
    if ($uri !== '/' && file_exists(__DIR__ . '/../public' . $uri)) {
        // Xác định MIME type
        $extension = pathinfo($uri, PATHINFO_EXTENSION);
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
        ];
        
        if (isset($mimeTypes[$extension])) {
            header('Content-Type: ' . $mimeTypes[$extension]);
        }
        
        readfile(__DIR__ . '/../public' . $uri);
        exit;
    }
    
    // Đặt lại REQUEST_URI để đảm bảo Laravel routing hoạt động
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';
}

// Đảm bảo APP_KEY nếu cần
putenv('APP_KEY=base64:'.base64_encode(random_bytes(32)));

// Load composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Khởi tạo application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Chạy application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response); 