#!/bin/bash

echo ""
echo "Starting installation"
echo ""

echo ""
echo "=================================================> 0%"
echo ""

echo ""
echo "1) Up the containers"
echo ""

docker-compose -p PICPAY up -d

echo ""
echo "=================================================> 12.5%"
echo ""

echo ""
echo "2) Creating file .env"
echo ""

docker exec picpay_transactions_application cp .env.example .env

echo ""
echo "=================================================> 25%"
echo ""

echo ""
echo "3) Installing dependencies by composer"
echo ""

docker exec picpay_transactions_application composer install --ignore-platform-req=php

echo ""
echo "=================================================> 37.5%"
echo ""

echo ""
echo "4) Running migrations"
echo ""

docker exec picpay_transactions_application php artisan migrate

echo ""
echo "=================================================> 50%"
echo ""

echo ""
echo "5) Running seeders"
echo ""

docker exec picpay_transactions_application php artisan db:seed

echo ""
echo "=================================================> 62.5%"
echo ""

echo ""
echo "6) Running unit tests"
echo ""

docker exec picpay_transactions_application vendor/bin/phpunit tests/Unit/ --testdox

echo ""
echo "=================================================> 75%"
echo ""

echo ""
echo "7) Running integration tests"
echo ""

docker exec picpay_transactions_application vendor/bin/phpunit tests/Integration/ --testdox

echo ""
echo "=================================================> 87.5%"
echo ""

echo ""
echo "8) Run the listener"
echo ""

docker exec picpay_transactions_application php artisan queue:listen

echo ""
echo "=================================================> 100%"
echo ""

echo ""
echo "Installation completed"
echo ""
