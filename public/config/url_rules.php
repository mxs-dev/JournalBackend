<?php
return [
    'api' => '/v1',
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/user', 'v1/group'],
        'pluralize'     => false,
        'tokens' => [
            '{id}' => '<id:\d+>',
        ],
        'extraPatterns' => [
            'POST' => 'create',
            'GET {id}' => 'view',
            'DELETE {id}'       =>  'delete',
        ]
    ]
];