<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '%iBtshYWjmyeap}E~$nL~YM$~{PQxa#7',
            'baseUrl' => '',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'prefix' => 'api',
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/user',
                        'v1/book',
                        'v1/article',
                    ]
                ],
                [
                    'prefix' => 'api',
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/auth' => '/v1/user'],
                    'patterns' => [
                        'POST' => 'auth',
                        '' => 'options',
                    ]
                ],
                [
                    'prefix' => 'api',
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/article/open' => '/v1/article'],
                    'patterns' => [
                        'POST <articleId:\d+>' => 'open',
                        '' => 'options',
                    ]
                ],
                [
                    'prefix' => 'api',
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/articles/my' => '/v1/article'],
                    'patterns' => [
                        'GET' => 'my',
                        '' => 'options',
                    ]
                ],
                [
                    'prefix' => 'api',
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/books/my' => '/v1/book'],
                    'patterns' => [
                        'GET' => 'my',
                        '' => 'options',
                    ]
                ]
            ],
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'format' =>  \yii\web\Response::FORMAT_JSON,
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null) {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'data' => $response->data,
                    ];
                    $response->statusCode = 200;
                }
            },
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
//        'errorHandler' => [
//            'errorAction' => 'site/error',
//        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
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
        'db' => $db,
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'modules' => [
        'v1' => [
            'class' => 'app\modules\v1\Module',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
