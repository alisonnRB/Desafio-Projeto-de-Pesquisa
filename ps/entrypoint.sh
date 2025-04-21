#!/bin/bash

cd /var/www

# Copia .env se não existir
if [ ! -f .env ]; then
    cp .env.example .env
    echo "Arquivo .env criado a partir do .env.example"
fi

# Gera chave do Laravel se ainda não existe
if ! grep -q "^APP_KEY=base64" .env; then
    php artisan key:generate
    echo "APP_KEY gerada"
fi

# Instala dependências se necessário
if [ ! -d vendor ]; then
    composer install --no-dev --optimize-autoloader
fi

# Cria o banco
php artisan migrate

# Sobe o server Laravel
php artisan serve --host=0.0.0.0 --port=8000
