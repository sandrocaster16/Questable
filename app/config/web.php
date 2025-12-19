<?php

use yii\rbac\DbManager;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'questable',
    'language' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@projectFolder' => realpath(dirname(__FILE__).'/../../'),
    ],
    'modules' => [
        'admin' => ['class' => 'app\modules\admin\Module'],
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'W3q1XR-3cSLP1IYsTa6zvs9Rw4A2XtDY',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'loginUrl' => ['auth/login'],
            'enableAutoLogin' => true,
            'on beforeAction' => function ($event) {
                if (Yii::$app->user->isGuest) {
                    Yii::$app->controller->redirect(['auth/login']);
                    $event->isValid = false;
                }
            },
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'assetManager' => [
            'appendTimestamp' => true,
            'bundles' => [
//                'yii\bootstrap5\BootstrapAsset' => [
//                    'css' => []
//                ],
            ],
        ],
        'authManager' => [
            'class' => DbManager::class,
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
//                'POST tg_api/telegram/login' => 'tg_api/telegram/login',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    'metronic8' => '@app/core/templates/crud/metronic-8',
                ]
            ]
        ],
        'allowedIPs' => ['*'],
    ];
}

Yii::setAlias('@upload', dirname(__DIR__) . '/pictures');

return $config;
