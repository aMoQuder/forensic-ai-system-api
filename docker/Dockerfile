FROM php:8.2-fpm

# تثبيت الأدوات الأساسية
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    nodejs \
    npm

# تثبيت PHP extensions المطلوبة
RUN docker-php-ext-install pdo pdo_mysql

# نسخ المشروع
COPY . /var/www

WORKDIR /var/www

# تثبيت Composer
RUN curl -sS https://getcomposer.org/installer | php
RUN php composer.phar install

# تثبيت npm dependencies
RUN npm install

# تشغيل Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

EXPOSE 8000
