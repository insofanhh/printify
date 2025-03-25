<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @php
        // Kiểm tra vị trí manifest.json
        $manifestPaths = [
            public_path('dist/.vite/manifest.json'),
            public_path('dist/manifest.json'),
            public_path('build/.vite/manifest.json'),
            public_path('build/manifest.json')
        ];
        
        // Dùng hàm này để kiểm tra và cập nhật biến môi trường VITE_MANIFEST_PATH
        foreach ($manifestPaths as $path) {
            if (file_exists($path)) {
                config(['vite.manifest_path' => $path]);
                break;
            }
        }
    @endphp
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    {{ $slot }}

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html> 