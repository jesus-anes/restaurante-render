#!/bin/bash
# Instalar dependencias
composer install

# Ejecutar las migraciones
php bin/console doctrine:migrations:migrate --no-interaction

# Ejecutar el comando final del contenedor (por ejemplo: php -S ...)
exec "$@"