<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Đảm bảo tìm đúng file manifest.json của Vite
        $manifestPaths = [
            public_path('dist/.vite/manifest.json'),
            public_path('dist/manifest.json'),
            public_path('build/.vite/manifest.json'),
            public_path('build/manifest.json')
        ];
        
        foreach ($manifestPaths as $path) {
            if (file_exists($path)) {
                config(['vite.manifest_path' => $path]);
                break;
            }
        }
    }
}
