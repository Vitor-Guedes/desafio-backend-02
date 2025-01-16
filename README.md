# desafio-backend-02
Repositorio para o desafio bravo

### Instalação

```bash
docker image build -t php-redis:1 .
```

```bash
docker run -p 127.0.0.1:8000:8000 -v $(pwd):/var/www/html --name php-redis php-redis:1 php -S 0.0.0.0:8000 -t /var/www/html/currency/public
```

```bash
docker exec -it php-redis bash
php artisan migrate
```