#!/bin/bash
# ==============================================================================
# SCRIPT TỰ ĐỘNG HÓA DEPLOYMENT CHO COMPUTER ACADEMY (LARAVEL + VUE INERTIA)
# Chạy script này trên VPS mỗi khi bạn cập nhật code: bash deploy.sh
# ==============================================================================

set -e

echo "🚀 [1/7] Bật chế độ bảo trì tạm thời..."
php artisan down || true

echo "📥 [2/7] Kéo mã nguồn mới nhất từ Git..."
git pull origin main

echo "📦 [3/7] Cài đặt dependencies PHP (Production)..."
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

echo "🗄️ [4/7] Cập nhật Database (Migration)..."
php artisan migrate --force

echo "🎨 [5/7] Biên dịch Frontend Assets (Vite & Vue 3)..."
npm install --no-audit --no-fund
npm run build

echo "⚡ [6/7] Tối ưu hóa và làm mới Cache hệ thống..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "🔐 Phân quyền thư mục lưu trữ (Storage & Cache)..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo "🔄 Khởi động lại PHP-FPM..."
sudo systemctl reload php8.3-fpm || sudo systemctl restart php8.3-fpm

echo "🟢 [7/7] Tắt chế độ bảo trì - Website đã sẵn sàng phục vụ!"
php artisan up

echo "✅ === TRIỂN KHAI THÀNH CÔNG RỰC RỠ! ==="
