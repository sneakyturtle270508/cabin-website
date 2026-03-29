#!/bin/bash

mkdir -p storage/framework/{cache/data,sessions,views}
mkdir -p storage/app/public
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p database
mkdir -p public/img
chmod -R 777 storage bootstrap/cache database public

exec apache2-foreground
