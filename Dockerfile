# Menggunakan image PHP dengan Alpine
FROM php:8.1-fpm-alpine

# Install ekstensi yang dibutuhkan untuk Laravel
RUN apk add --no-cache \
    bash \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libxpm-dev \
    zlib-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-install gd pdo pdo_mysql zip xml

# Mengatur direktori kerja
WORKDIR /var/www

# Menyalin file composer.json dan composer.lock untuk instalasi dependensi
COPY composer.json composer.lock ./

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependensi Laravel
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Menyalin seluruh file proyek ke dalam container
COPY . .

# Menetapkan hak akses yang benar
RUN chown -R www-data:www-data /var/www

# Mengatur port yang digunakan
EXPOSE 9000

CMD ["php-fpm"]
