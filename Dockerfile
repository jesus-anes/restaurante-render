# Usa PHP con extensiones necesarias
FROM php:8.2-cli

# Instala extensiones de PHP necesarias para Symfony
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

#Symfony/cli
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia los archivos de la app al contenedor
COPY . .

# Expone el puerto 9000
EXPOSE 9000

# Comando por defecto: servidor PHP embebido
CMD ["php", "-S", "0.0.0.0:9000", "-t", "public"]
