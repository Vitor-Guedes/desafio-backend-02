# Use uma imagem base com PHP-FPM
FROM php:8.1-fpm

# Instale Redis e extensões do PHP necessárias
RUN apt-get update && apt-get install -y \
    redis \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Install xdebug
RUN pecl install xdebug-3.4.0beta1 && docker-php-ext-enable xdebug

RUN ln -snf /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime && echo "America/Sao_Paulo" > /etc/timezone

# Copie um script de inicialização personalizado
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY xdebug.ini "${PHP_INI_DIR}/conf.d"

EXPOSE 9000

USER root

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
RUN chown -R www-data:www-data /var/www/html

# Defina o comando de inicialização
CMD ["start.sh"]