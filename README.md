# desafio-backend-02
Repositorio para o desafio bravo

# Requisitos e Versões
![Static Badge](https://img.shields.io/badge/Docker-27.5.0-blue)

![Static Badge](https://img.shields.io/badge/PHP-8.1-blue)

![Static Badge](https://img.shields.io/badge/SQLite-blue)

![Static Badge](https://img.shields.io/badge/Redis-7.0-red)

![Static Badge](https://img.shields.io/badge/Laravel--Framework--Lumen-10.0.4-orange)

# Instalação

Após Clonar o repositório, dentro do diretório onde existe o arquivo Dockerfile execute o comando a baixo para criar a imagem docker:

```bash
docker image build -t php-redis:1 .
```

Com a imagem criada rode o seguinte comando para subir o container e criar o servidor php:

```bash
docker run -d -p 127.0.0.1:8000:8000 -v $(pwd):/var/www/html --name php-redis php-redis:1 php -S 0.0.0.0:8000 -t /var/www/html/currency/public
```

Entre no container e rode os comandos para criar as tabelas necessária para a aplicação e subir o  servidor do redis:

```bash
docker exec -it php-redis bash

# Inicia o servidor do redis
service redis-server start

# Diretório - var/www/html/currency 
composer install
# ou
composer update
# Cria .env
php -r "file_exists('.env') || copy('.env.example', '.env');"

php artisan migrate
```

**Obs**: Verificar se o arquivo **.env** existe e esta com os parametros corretos.

# Testes

```bash
# Diretório - var/www/html/currency 
vendor/bin/phpunit
```

# Aplicação

Para cotação das moedas existentes (USD, BRL, EUR, BTC, ETH) vai ser consultada a api externa [https://economia.awesomeapi.com.br/](https://economia.awesomeapi.com.br/) para o dia atual.
A apliação vai permitir o cadastro de novas moedas seguindo o mesmo padrão da api externa.

---

**Api** - Gerenciamento das moedas e cotações.

## currency

<details open>
<summary> <code>POST /currency (Cria nova quotação para a moeda) </code> </summary>

Cada inserção nesse endpoint criar um registro na base que vai servir de histórico do valor da moeda.

### Request - Parameters
> | name | type | data type | description |
> | ---- | ---- | --------- | ----------- |
> | code | min:3, max:5, required (USD)| string | Código da moeda de lastro (padrão USD) |
> | codein | min:3, max:5, required (D&D)| string | Código da moeda |
> | description | min:10, max:100, required| string | Descrição da Moeda (Dolar Americano / D&D Peça de ouro) |
> | bid | required | float (10,4) | Valor de compra da moeda (1 unidade - 1$) |
> | ask | required | float (10,4) | Valor de venda da moeda (1 unidade - 1$) |

### Request - Exemplo

#### Headers

> | name | value |
> | ---- | ----- |
> | Accept | application/json |
> | Content-type | application/json |

#### Body

```json
{
    "code": "USD",
    "codeIn": "D&D",
    "description": "Dolar Americano/D&D Peça de ouro",
    "bid": 2.2500,
    "ask": 2.2500
}
```

### Responses
> | HTTP Code | Content-Type | Body |
> | --------- | ------------ | ---- |
> | 201 | application/json | |
> | 422 | application/json | {"error": {"message": "Atributo x é obrigatório"}} |
</details>

---

<details>
<summary> <code>GET /currency/{:code-:codein} (Consulta cotação da moeda) </code> </summary>

Consulta a cotação das moedas informadas na url.

### Request - Parameters
> | name | type | data type | description |
> | ---- | ---- | --------- | ----------- |
> | code | min:3, max:5, required (D&D)| string | Código da moeda - de lastro (padrão USD) |
> | codein | min:3, max:5, required (D&D)| string | Código da moeda |

### Exemplo
```
http://localhost:8000/currency/USD-D&D
```

### Responses
> | HTTP Code | Content-Type | Body |
> | --------- | ------------ | ---- |
> | 200 | application/json | [{"code":"USD","codein":"D&D","name":"Dólar Americano/D&D$ peça de ouro","high":"6.0708","low":"5.9935","varBid":"0.0064","pctChange":"0.11","bid":"6.0558","ask":"6.0568","timestamp":"1737118799","create_date":"2025-01-17 09:59:59"}] |
> | 500 | application/json | {"error": {"message": "Erro interno"}} |
</details>

## convert

<details>
<summary> <code>GET /convert?from={:from}&to={:to}&amount={:amount} (Conversão de valores) </code> </summary>

Converte o valor do parametro **amount** da moeda **from** para a moeda **to**. O valor informado vai ser convertido de acordo com a ultima cotação da :from-:to.

### Request - Parameters
> | name | type | data type | description |
> | ---- | ---- | --------- | ----------- |
> | from | min:3, max:5, required (D&D)| string | Código da moeda - de lastro (padrão USD) |
> | to | min:3, max:5, required (D&D)| string | Código da moeda |
> | amount | min:0, required| flaot (10, 4) | Valor para conversão |

### Exemplo
```
http://localhost:8000/convert?from=USD&to=D%26D&amount=2.00
```

### Responses
> | HTTP Code | Content-Type | Body |
> | --------- | ------------ | ---- |
> | 200 | application/json | {"from": "USD", "to": "D&D", "amount": 2.00, "change": 4.00} |
> | 422 | application/json | {"error": {"message": "Atributo x é obrigatório"}} |
> | 500 | application/json | {"error": {"message": "Erro interno"}} |
</details>