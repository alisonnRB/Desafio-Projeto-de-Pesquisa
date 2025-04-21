FROM php:8.3-cli

# Instala pacotes necessários, incluindo o netcat
RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libmariadb-dev-compat \
    libmariadb-dev \
    netcat-openbsd

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Configura o diretório de trabalho
WORKDIR /var/www

# Copia o código-fonte
COPY . .

# Copia o script de entrypoint e garante permissões de execução
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expõe a porta para o Laravel
EXPOSE 8000

# Comando para rodar o script de entrypoint
CMD ["/entrypoint.sh"]
