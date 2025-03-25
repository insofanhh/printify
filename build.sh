#!/bin/bash

# Cài đặt PHP dependencies
echo "🔧 Cài đặt PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Cài đặt NPM dependencies và build assets
echo "🔧 Cài đặt NPM dependencies và build assets..."
npm ci
npm run build

# Tạo thư mục storage
echo "🔧 Thiết lập thư mục storage..."
mkdir -p public/storage

# Tạo file .env từ example
echo "🔧 Tạo file .env..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Tạo app key nếu chưa có
echo "🔧 Tạo App Key..."
php artisan key:generate --force

# Clear cache
echo "🔧 Xóa cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Tối ưu
echo "🔧 Tối ưu hóa..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Đảm bảo có thư mục output cần thiết
echo "🔧 Đảm bảo có thư mục output cần thiết..."
mkdir -p public/build

# Tạo symbolic link (không cần thiết trên Vercel nhưng giữ cho nhất quán)
echo "🔧 Tạo symbolic link..."
php artisan storage:link || true

# Đảm bảo file manifest.json có ở thư mục đúng
echo "🔧 Kiểm tra manifest.json..."
if [ -f "public/build/.vite/manifest.json" ] && [ ! -f "public/build/manifest.json" ]; then
    cp public/build/.vite/manifest.json public/build/manifest.json
fi

# Tạo file .htaccess trong public
echo "🔧 Tạo file .htaccess trong public..."
cat > public/.htaccess << 'EOL'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

<FilesMatch "\.php$">
    SetHandler application/x-httpd-php
</FilesMatch>
EOL

echo "✅ Build hoàn tất!" 