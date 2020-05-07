<?php

$params = require __DIR__ . '/params.php';
$db     = require __DIR__ . '/db.php';

return [
    'id'                  => 'micro-app',
    // the basePath of the application will be the `micro-app` directory
     'basePath'            => dirname(__DIR__),
    'modules'             => [
        'v1' => [
            'class' => 'app\modules\v1\v1',
        ],
    ],
    // this is where the application will find all controllers
     'controllerNamespace' => 'app\controllers',
    // set an alias to enable autoloading of classes from the 'micro' namespace
     'aliases'             => [
        '@app' => __DIR__ . '/../',
    ],
    'components'          => [
        'urlManager' => [
            'class'               => 'yii\web\UrlManager',
            'enablePrettyUrl'     => true,
            'showScriptName'      => false,
            'enableStrictParsing' => true,
            'rules'               => [
                ''                                              => 'site',
                '<action:[\w-]+>'                               => 'site',
                'api/v1/index'                                  => 'v1/api/index',
                'api/v1/<date:\d{4}-\d{2}-\d{2}>/<code:[\w-]+>' => 'v1/api/filter',
                'api/v1/<date:\d{4}-\d{2}-\d{2}>'               => 'v1/api/filter',
                'api/v1/<code:[\w-]+>'                          => 'v1/api/filter',
                'api/v1/yearly/<year:\d{4}>'                    => 'v1/api/yearly',
                'api/v1/tendays'                                => 'v1/api/tendays',
                'api/v1/daily'                                  => 'v1/api/daily',
                'api/v1/'                                       => 'v1/api/filter',
            ],
        ],
        'user'       => [
            'identityClass'   => 'app\models\UserIdentity',
            'enableAutoLogin' => false,
            'enableSession'   => false,
            'loginUrl'        => null,
        ],
        'request'    => [
            'parsers'          => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'enableCsrfCookie' => false,
        ],
        'db'         => $db,
    ],
    'params'              => $params,
];
