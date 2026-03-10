# Stage 1: Build Assets (Vue.js)
FROM node:25-alpine AS assets-builder
WORKDIR /app
COPY package*.json ./
RUN npm install --force
COPY . .
RUN npm run build

# Stage 2: FrankenPHP (Chạy Laravel trực tiếp)
FROM dunglas/frankenphp:1.1-php8.2-alpine

# Cài đặt extension cần thiết cho Laravel
RUN install-php-extensions pdo_mysql bcmath gd intl zip opcache

WORKDIR /app

# Copy toàn bộ code
COPY . .
# Copy assets từ stage 1
COPY --from=assets-builder /app/public/build ./public/build

# Cài đặt Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Cấp quyền
RUN chown -R www-data:www-data storage bootstrap/cache

# Thiết lập biến môi trường để FrankenPHP chạy đúng thư mục public
ENV SERVER_NAME=:80
ENV PHP_INI_SCAN_DIR=:
ENV APP_RUNTIME=Laravel\Octane\FrankenPHP\Runtime

EXPOSE 80
