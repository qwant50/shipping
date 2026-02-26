#!/usr/bin/env bash

echo 'Install thgyii2 composer packages.'
[ -f /app/composer.json ] && (cd /app && composer install -n)

echo 'Clean caches.'
rm -rf /app/console/runtime/cache/*

echo 'Application container has been started.'

exec "$@"
