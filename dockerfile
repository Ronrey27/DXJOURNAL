# 1. Usamos la imagen oficial de Node 24 para sacar los archivos
FROM node:24-slim AS node_source

# 2. Tu imagen base de Laravel
FROM shinsenter/laravel:php8.4-nginx

# 3. Forzamos la sustitución de Node y NPM
# Copiamos el binario y las librerías de la versión 24
COPY --from=node_source /usr/local/bin/node /usr/local/bin/node
COPY --from=node_source /usr/local/lib/node_modules /usr/local/lib/node_modules

# Re-creamos los enlaces simbólicos para que 'npm' apunte a la v24
RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -sf /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# Verificación opcional durante la construcción
RUN node -v && npm -v

# --- El resto de tu configuración actual ---
WORKDIR /var/www/html
COPY package*.json composer*.json ./
RUN composer install --no-interaction --optimize-autoloader --no-scripts
RUN npm install
COPY . .
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80 5173
