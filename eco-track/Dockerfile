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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_sqlite zip \
    && a2enmod rewrite

# Instalar Node.js e npm (última versão estável LTS)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Definir o diretório de trabalho para a aplicação Laravel
WORKDIR /var/www/html

# Copiar os arquivos da aplicação para o container
COPY . .

# Criar um novo .env a partir do .env.example, caso não exista
RUN if [ ! -f /var/www/html/.env ]; then cp .env.example .env; fi

# Ajustar as permissões dos arquivos para o Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Criar o banco de dados SQLite, se não existir
RUN if [ ! -f /var/www/html/database/database.sqlite ]; then touch /var/www/html/database/database.sqlite; fi

# Garantir que o banco de dados SQLite tenha as permissões corretas
RUN chmod -R 777 /var/www/html/database/database.sqlite \
    && chown -R www-data:www-data /var/www/html/database \
    && chmod -R 775 /var/www/html/database

# Ajustar o DocumentRoot do Apache para o diretório public do Laravel
RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Rodar o Composer para instalar as dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Instalar dependências do Vite e rodar o build
RUN npm install && npm run build

# Rodar as migrações com seed (somente se o banco de dados SQLite estiver configurado corretamente)
RUN php artisan migrate --seed --force

# Gerar a chave da aplicação Laravel
RUN php artisan key:generate

# Garantir que os arquivos estáticos gerados pelo Vite (CSS/JS) tenham permissões corretas
RUN chmod -R 777 /var/www/html/public/build/assets

# Expor a porta 80 para acesso externo
EXPOSE 80

# Iniciar o Apache em primeiro plano
CMD ["apache2-foreground"]
