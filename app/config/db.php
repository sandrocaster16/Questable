<?php

return [
    'class' => 'yii\db\Connection',
    // Почему прописано mysql вместо localhost? https://stackoverflow.com/questions/46723215/docker-sqlstatehy000-2002-no-such-file-or-directory
    // 'dsn' => 'mysql:host=mysql;dbname=questable',
    // 'username' => $_ENV['DB_USER'],
    // 'password' => $_ENV['DB_PASSWORD'],
    // 'charset' => 'utf8',

    'dsn' => $_ENV['DB_DSN'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
