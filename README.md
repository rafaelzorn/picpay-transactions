<h3 align="center">PicPay Transactions</h3>

<p>
    <a href="#Instalação">Instalação</a>
</p>

## Instalação

#### Pré-requisitos

Antes de começar, certifique-se que você tem o Docker instalando na sua máquina.

```bash
# Clone este repositório
$ git clone git@github.com:rafaelzorn/picpay-transactions.git

# Acesse a pasta docker que está na raiz do projeto
$ cd docker

# Execute o script installer.sh
$ ./installer.sh
```

Ao executar o script installer.sh ele irá executar os seguintes comandos:

- docker-compose up -d
- docker exec picpay-transactions-api cp .env.example .env
- docker exec picpay-transactions-api composer install --ignore-platform-req=php
- docker exec picpay-transactions-api php artisan migrate
- docker exec picpay-transactions-api php artisan db:seed
- docker exec picpay-transactions-api php artisan queue:listen
- docker exec picpay-transactions-api vendor/bin/phpunit tests/Integration/ --testdox
- docker exec picpay-transactions-api vendor/bin/phpunit tests/Unit/ --testdox

Após o processo do script installer.sh, você deve criar uma Queue no RabbitMk, conforme imagem abaixo:
