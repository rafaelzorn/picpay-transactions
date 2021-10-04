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

docker-compose up -d

echo ""
echo "=================================================> 14.2%"
echo ""

echo ""
echo "2) Creating file .env"
echo ""

docker exec picpay-transactions-api cp .env.example .env

echo ""
echo "=================================================> 28%"
echo ""

echo ""
echo "3) Installing dependencies by composer"
echo ""

docker exec picpay-transactions-api composer install --ignore-platform-req=php

echo ""
echo "=================================================> 42.6%"
echo ""

echo ""
echo "4) Running migrations"
echo ""

docker exec picpay-transactions-api php artisan migrate

echo ""
echo "=================================================> 56.8%"
echo ""

echo ""
echo "5) Running seeders"
echo ""

docker exec picpay-transactions-api php artisan db:seed

echo ""
echo "=================================================> 71%"
echo ""

echo ""
echo "6) Running integration tests"
echo ""

docker exec picpay-transactions-api vendor/bin/phpunit tests/Integration/ --testdox

echo ""
echo "=================================================> 85.2%"
echo ""

echo ""
echo "7) Running unit tests"
echo ""

docker exec picpay-transactions-api vendor/bin/phpunit tests/Unit/ --testdox

echo ""
echo "=================================================> 100%"
echo ""

echo ""
echo "Installation completed"
echo ""
