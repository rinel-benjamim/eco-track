# Usando uma imagem base do PHP 8.2 com Apache
FROM php:8.2-apache

# Instalar dependências do sistema e extensões PHP necessárias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    sqlite3 \
    libsqlite3-dev \
    curl \
    wget \
    openjdk-17-jre \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_sqlite zip bcmath \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar OpenAPI Generator CLI manualmente
RUN wget -O /usr/local/bin/openapi-generator-cli https://repo1.maven.org/maven2/org/openapitools/openapi-generator-cli/7.0.1/openapi-generator-cli-7.0.1.jar \
    && chmod 755 /usr/local/bin/openapi-generator-cli

# Criar um alias para facilitar o uso do OpenAPI Generator
RUN echo '#!/bin/sh\nexec java -jar /usr/local/bin/openapi-generator-cli "$@"' > /usr/local/bin/openapi-generator \
    && chmod +x /usr/local/bin/openapi-generator

# Definir o diretório de trabalho para a aplicação Laravel
WORKDIR /var/www/html

# Copiar os arquivos da aplicação para o container
COPY . .

# Criar um novo .env a partir do .env.example, caso não exista
RUN cp .env.example .env || true

# Ajustar permissões dos arquivos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Criar o banco de dados SQLite, se não existir
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && chmod 777 /var/www/html/database/database.sqlite

# Ajustar permissões para storage e cache
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Ajustar o DocumentRoot do Apache para o diretório public do Laravel
RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Rodar o Composer para instalar as dependências do Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Instalar dependências do Vite e rodar o build
RUN npm ci && npm run build

# Instalar Faker (se necessário)
RUN composer require fakerphp/faker --no-interaction --no-scripts

# Instalar o L5 Swagger
RUN composer require "darkaonline/l5-swagger" --no-interaction --no-scripts

# Gerar a documentação do Swagger
RUN php artisan l5-swagger:generate

# Rodar as migrações com seed
RUN php artisan migrate --seed --force

# Gerar a chave da aplicação Laravel
RUN php artisan key:generate

# Criar cache de configuração para otimizar o Laravel
RUN php artisan config:cache && php artisan route:cache

# Garantir que os arquivos estáticos do Vite tenham permissões corretas
RUN chmod -R 777 /var/www/html/public/build/assets

# Expor a porta 80 para acesso externo
EXPOSE 80

# Comando final para rodar o Apache e o Laravel
CMD ["apache2-foreground"]
