<?php

return [
    'request' => [
        'baseUrl' => '',
        'cookieValidationKey' => 'cookieValidationKey',
        'parsers' => [
            'application/json' => 'yii\web\JsonParser',
        ]
    ],
    'cache' => [
        'class' => 'yii\caching\FileCache',
    ],
    'user' => [
        'identityClass' => 'app\models\User',
        'enableAutoLogin' => true,
    ],
    'errorHandler' => [
        'errorAction' => 'site/error',
    ],
    'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
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
    'urlManager' => [
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'rules' => include('url_rules.php'),
    ],
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
    ],
    'response' => [
        'class' => 'yii\web\Response',
        'on beforeSend' => function ($event) {
            $response = $event->sender;

            if($response->format == 'html') {
                return $response;
            }

            $responseData = $response->data;

            if(is_string($responseData) && json_decode($responseData)) {
                $responseData = json_decode($responseData, true);
            }


            if($response->statusCode >= 200 && $response->statusCode <= 299) {
                $response->data = [
                    'success'   => true,
                    'status'    => $response->statusCode,
                    'data'      => $responseData,
                ];
            } else {
                $response->data = [
                    'success'   => false,
                    'status'    => $response->statusCode,
                    'data'      => $responseData,
                ];
            }
            return $response;
        }
    ]
];