<?php
return [
    'api' => '/v1',
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/user'],
        'pluralize'     => false,
        'tokens' => [
            '{id}' => '<id:\d+>',
        ],
        'extraPatterns' => []
    ],
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/group'],
        'pluralize'     => false,
        'tokens' => [
            '{id}' => '<id:\d+>',
            '{groupId}' => '<groupId:\d+>',
        ],
        'extraPatterns' => [
            '{groupId}/students' => 'students',
        ]
    ],
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/teacher'],
        'pluralize'     => false,
        'tokens' => [
            '{id}' => '<id:\d+>',
            '{teacherId}' => '<teacherId:\d+>',
        ],
        'extraPatterns' => [
            '{teacherId}/<action>' => '<action>',
        ]
    ]
];