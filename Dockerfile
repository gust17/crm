# Usar uma imagem PHP com Apache
FROM php:8.1-apache

# Atualizar lista de pacotes e instalar dependências necessárias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões do PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


RUN apt-get update && apt-get install -y \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl


# Permitir Composer como superusuário
ENV COMPOSER_ALLOW_SUPERUSER 1

# Habilitar o mod_rewrite e headers para o Apache
RUN a2enmod rewrite headers

# Configurar php.ini para exibir erros
RUN echo 'display_errors = On' >> /usr/local/etc/php/conf.d/docker-php-display-errors.ini

# Copiar o aplicativo Laravel para o contêiner
COPY . /var/www/html

# Mudar para o diretório /var/www/html
WORKDIR /var/www/html

# Atualizar e instalar dependências com Composer
RUN composer update --no-interaction --optimize-autoloader --no-dev
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Copiar .env.example para .envversion: '3.8'
                               #services:
                               #  app:
                               #    build:
                               #      context: .
                               #    container_name: laravel_app
                               #    restart: unless-stopped
                               #    ports:
                               #      - "8000:80"
                               #    volumes:
                               #      - .:/var/www/html
                               #    depends_on:
                               #      - db
                               #    environment:
                               #      DB_HOST: db
                               #      DB_PORT: 3306
                               #      DB_DATABASE: crm
                               #      DB_USERNAME: gus
                               #      DB_PASSWORD: 12345678
                               #
                               #  db:
                               #    image: mariadb:latest
                               #    container_name: laravel_db
                               #    restart: unless-stopped
                               #    environment:
                               #      MYSQL_ROOT_PASSWORD: root
                               #    ports:
                               #      - "3306:3306"
                               #    volumes:
                               #      - dbdata:/var/lib/mysql
                               #      - ./docker/db-init:/usr/local/etc/mysql
                               #    command: ["--init-file=/usr/local/etc/mysql/init.sql"]
                               #
                               #  phpmyadmin:
                               #    image: phpmyadmin/phpmyadmin
                               #    container_name: laravel_phpmyadmin
                               #    restart: unless-stopped
                               #    ports:
                               #      - "8080:80"
                               #    environment:
                               #      PMA_HOST: db
                               #      PMA_USER: gus
                               #      PMA_PASSWORD: 12345678
                               #      MYSQL_ROOT_PASSWORD: root
                               #    depends_on:
                               #      - db
                               #
                               #volumes:
                               #  dbdata:
RUN if [ -f .env.example ]; then cp .env.example .env; fi

# Preencher dados do banco de dados no .env
RUN if [ -f .env ]; then \
    sed -i 's/DB_DATABASE=laravel/DB_DATABASE=crm/' .env && \
    sed -i 's/DB_USERNAME=root/DB_USERNAME=gus/' .env && \
    sed -i 's/DB_PASSWORD=/DB_PASSWORD=12345678/' .env; \
    fi

# Gerar a chave do aplicativo Laravel
RUN if [ -f artisan ]; then php artisan key:generate; fi

# Configurar a pasta raiz do Apache para a pasta public do Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Alterar o dono do diretório para o usuário www-data e ajustar permissões
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html/storage -type f -exec chmod 664 {} \; \
    && find /var/www/html/storage -type d -exec chmod 775 {} \; \
    && chmod 775 /var/www/html/storage/logs \
    && chmod 775 /var/www/html/bootstrap/cache

# Expor a porta 80
EXPOSE 80
