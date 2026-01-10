# Gunakan PHP 8.2 FPM berbasis Debian Bookworm
FROM php:8.2-fpm

# 1. Install system dependencies
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

# 3. Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 4. Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Install Bun secara resmi
RUN curl -fsSL https://bun.sh/install | bash
# Daftarkan Bun ke PATH agar bisa dipanggil langsung
ENV PATH="/root/.bun/bin:${PATH}"

# 6. Set working directory
WORKDIR /var/www

# 7. Copy seluruh file project
COPY . .

# 8. Penanganan .env
RUN cp .env.production .env

# 9. Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 10. Install Node dependencies & Build assets menggunakan BUN
# Bun jauh lebih cepat dan hemat RAM dibandingkan NPM
RUN bun install
RUN bun run build

# 11. Copy konfigurasi Nginx
COPY nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# 12. Optimasi Laravel
RUN php artisan config:cache && php artisan route:cache

# 13. Atur Permission
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# 14. Expose port 8080
EXPOSE 8080
# 15. Jalankan Nginx dan PHP-FPM
CMD service nginx start && php-fpm