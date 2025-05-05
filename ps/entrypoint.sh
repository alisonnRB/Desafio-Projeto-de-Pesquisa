cd /var/www

# Copia .env se não existir
if [ ! -f .env ]; then
    cp .env.example .env
    cp .env.testing .env.testing
    echo "Arquivo .env criado a partir do .env.example"
fi

# Gera chave do Laravel se ainda não existe
if ! grep -q "^APP_KEY=base64" .env; then
    php artisan key:generate
    echo "APP_KEY gerada"
fi

# Limpa cache (depois das chaves)
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Instala dependências se necessário
if [ ! -d vendor ]; then
    composer install
fi

composer self-update
composer require -dev phpunit/phpunit

if ! grep -q "^APP_KEY=base64" .env.testing; then
    php artisan key:generate --env=testing
    echo "APP_KEY gerada em testing"
fi
# Cria o banco
php artisan migrate

php artisan registrar:oidc-client

npm install

# necessário para execução do build vite
npx vite build

# Sobe o server Laravel
php artisan serve --host=0.0.0.0 --port=8000
