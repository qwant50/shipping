<?php

return [
    'id' => 'app-tests',
    'basePath' => dirname(__DIR__),
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => sprintf(
                'pgsql:host=%s;port=%s;dbname=app_test',
                getenv('APP_DB_HOST') ?: 'shipping-db',
                getenv('APP_DB_PORT') ?: '5432'
            ),
            'username' => getenv('APP_DB_USER') ?: 'app',
            'password' => getenv('APP_DB_PASSWORD') ?: 'app',
            'charset' => 'utf8',
        ],
    ],
];
