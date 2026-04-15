# Usamos la base que ya te funcionó
FROM shinsenter/laravel:php8.4-nginx

# Instalamos Node.js y npm usando apt-get (Debian/Ubuntu)
RUN apt-get update && apt-get install -y \
    nodejs \
    npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Establecemos el directorio de trabajo
WORKDIR /var/www/html

# Copiamos solo los archivos de dependencias primero (mejor para la caché)
COPY package*.json composer*.json ./

# Instalamos dependencias
RUN composer install --no-interaction --optimize-autoloader --no-scripts
RUN npm install

# Copiamos el resto del proyecto
COPY . .

# Permisos para Arch Linux/Docker
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80 5173
