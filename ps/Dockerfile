FROM php:8.3-cli

# Instala extensões PHP necessárias e dependências do Composer
RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Instala o Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Cria diretório de trabalho
WORKDIR /var/www

# Copia os arquivos do app
COPY . .

# Instala as dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Expõe a porta do Laravel
EXPOSE 8000

# Comando padrão: serve a aplicação Laravel
CMD ["/bin/bash", "-c", "composer install --no-dev --optimize-autoloader && php artisan serve --host=0.0.0.0 --port=8000", "php artisan key:generate"]

