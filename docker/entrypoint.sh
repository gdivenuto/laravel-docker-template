#!/bin/bash
set -e

cd /var/www/html

echo "Iniciando contenedor Laravel..."

if [ -f "artisan" ]; then

    if [ ! -f ".env" ]; then
        echo "Creando archivo .env..."
        cp .env.example .env

        sed -i "s/^DB_HOST=.*/DB_HOST=mysql/" .env
        sed -i "s/^DB_PORT=.*/DB_PORT=3306/" .env
        sed -i "s/^DB_DATABASE=.*/DB_DATABASE=${MYSQL_DATABASE}/" .env
        sed -i "s/^DB_USERNAME=.*/DB_USERNAME=${MYSQL_USER}/" .env
        sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${MYSQL_PASSWORD}/" .env
    fi

    if [ ! -d "vendor" ]; then
        echo "Instalando dependencias Composer..."
        composer install
    fi

    if grep -q "^APP_KEY=$" .env || ! grep -q "^APP_KEY=" .env; then
        echo "Generando APP_KEY..."
        php artisan key:generate
    fi

    if [ ! -L "public/storage" ]; then
        echo "Creando link simbólico storage..."
        php artisan storage:link
    fi

    chown -R www-data:www-data storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache

else
    echo "No se detectó Laravel todavía en /var/www/html."
fi

echo "Apache iniciado."
apache2-foreground