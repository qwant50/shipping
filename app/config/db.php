<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        getenv('APP_DB_HOST') ?: 'shipping-db',
        getenv('APP_DB_PORT') ?: '5432',
        getenv('APP_DB') ?: 'app'
    ),
    'username' => getenv('APP_DB_USER') ?: 'app',
    'password' => getenv('APP_DB_PASSWORD') ?: 'app',
    'charset' => 'utf8',
];
