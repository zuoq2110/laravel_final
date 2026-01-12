# 1️⃣ Base image PHP-FPM với extensions cần thiết
FROM php:8.2-fpm

# 2️⃣ Cài thêm các extension cho Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libzip-dev \
    unzip \
    git \
    curl \
 && docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd \
 && rm -rf /var/lib/apt/lists/*

# 3️⃣ Cài Composer (quản lý PHP packages)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4️⃣ Set working directory
WORKDIR /var/www/html

# 5️⃣ Copy toàn bộ code vào container
COPY . .

# 6️⃣ Cài dependencies Laravel
RUN composer install --no-dev --optimize-autoloader

# 7️⃣ Set permissions (nếu cần cho storage và cache)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8️⃣ Expose port FastCGI (PHP-FPM mặc định)
EXPOSE 9000

# 9️⃣ Start PHP-FPM
CMD ["php-fpm"]
