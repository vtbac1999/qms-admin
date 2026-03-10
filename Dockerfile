# Sử dụng FrankenPHP bản Alpine cho nhẹ và tương thích M4
FROM dunglas/frankenphp:1.1-php8.4-alpine

# 1. Cài đặt các công cụ hệ thống & PHP extensions cần thiết cho Laravel
RUN install-php-extensions pdo_mysql bcmath gd intl zip opcache

# 2. Cài đặt Node.js và Composer vào cùng một container để build
RUN apk add --no-cache nodejs npm
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 3. Copy toàn bộ source code vào
COPY . .

# 4. Cài đặt PHP dependencies (Để có thư mục vendor/tightenco/ziggy)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# 5. Cài đặt Node dependencies và Build Assets (Vue/Vite)
# Dùng --legacy-peer-deps để tránh lỗi xung đột phiên bản starter kit
RUN npm install --force
RUN npm run build

# 6. Dọn dẹp node_modules sau khi build để giảm dung lượng image (tùy chọn)
RUN rm -rf node_modules

# 7. Thiết lập quyền hạn cho Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Cấu hình FrankenPHP chạy cổng 80 nội bộ
ENV SERVER_NAME=:80
ENV APP_RUNTIME=Laravel\Octane\FrankenPHP\Runtime

EXPOSE 80