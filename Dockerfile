# Gunakan PHP 8.2 FPM berbasis Debian Bookworm
FROM php:8.2-fpm

# 1. Install system dependencies (Termasuk libzip-dev untuk error openspout tadi)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx

# 2. Clean cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Install PHP extensions (Pastikan zip ada di sini)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 4. Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Install Node.js 20 LTS (Ini solusi untuk error crypto.hash)
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs

# 6. Set working directory
WORKDIR /var/www

# 7. Copy seluruh file project
COPY . .

# 8. Penanganan .env
# Pastikan file .env.production tidak di-ignore oleh .gcloudignore atau .dockerignore
RUN cp .env.production .env

# 9. Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 10. Install Node dependencies & Build assets
# Menambahkan --openssl-legacy-provider sebagai antisipasi tambahan
RUN npm install
RUN export NODE_OPTIONS=--openssl-legacy-provider && npm run build

# 11. Copy konfigurasi Nginx
COPY nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# 12. Optimasi Laravel
# PENTING: Jalankan setelah composer install dan .env tersedia
RUN php artisan config:cache && php artisan route:cache

# 13. Atur Permission
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# 14. Expose port 8080
EXPOSE 8080

# 15. Jalankan Nginx dan PHP-FPM
# Menggunakan 'daemon off' untuk Nginx agar container tetap running
CMD service nginx start && php-fpm