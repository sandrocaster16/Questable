<?php

return [
    'class' => 'yii\db\Connection',
    
    // Собираем DSN строку из отдельных, понятных переменных из .env
    'dsn' => 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'],
    
    // Используем правильные имена переменных
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];