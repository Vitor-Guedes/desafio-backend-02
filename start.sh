#!/bin/bash

# Inicia o Redis em segundo plano
redis-server --daemonize yes

# Inicia o PHP-FPM
php-fpm

chown -R www-data:www-data /var/www/html