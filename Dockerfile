# Gunakan PHP 8.2 FPM
FROM php:8.2-fpm

# 1. Install system dependencies (Tetap butuh libzip untuk Laravel)
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip nginx

# 2. Clean cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 4. Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Set working directory
WORKDIR /var/www

# 6. Copy seluruh file project
# Karena sudah build di lokal, folder public/build otomatis ikut masuk ke container
COPY . .

# 7. Penanganan .env
RUN cp .env.production .env

# 8. Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 9. Copy konfigurasi Nginx
COPY nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
RUN php artisan storage:link
# 10. Optimasi Laravel
# Sangat penting dijalankan setelah composer install
RUN php artisan config:cache
# RUN php artisan route:cache

# 11. Atur Permission
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN sed -i 's/client_max_body_size .*/client_max_body_size 30M;/' /etc/nginx/nginx.conf || echo "client_max_body_size 30M;" >> /etc/nginx/conf.d/default.conf
# Tambahkan ini sebelum perintah CMD atau ENTRYPOINT
RUN echo "upload_max_filesize=25M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=30M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini
# 12. Expose port 8080
EXPOSE 8080

# 13. Jalankan Nginx dan PHP-FPM
CMD service nginx start && php-fpm