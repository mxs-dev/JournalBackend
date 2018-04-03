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
    ], // UserController
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/group'],
        'pluralize'     => false,
        'tokens' => [
            '{id}'        => '<id:\d+>',
            '{groupId}'   => '<groupId:\d+>',
            '{studentId}' => '<studentId:\d+>'
        ],
        'extraPatterns' => [
            '{groupId}/students'                => 'students',
            '{groupId}/add-student/{studentId}' => 'add-student',
            '{groupId}/rm-student/{studentId}'  => 'remove-student',
        ]
    ], // GroupController
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/teacher'],
        'pluralize'     => false,
        'tokens' => [
            '{id}' => '<id:\d+>',
            '{teacherId}' => '<teacherId:\d+>',
        ],
        'extraPatterns' => [
        ]
    ], // TeacherController
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/subject'],
        'pluralize'     => false,
        'tokens' => [
            '{id}' => '<id:\d+>',
        ],
        'extraPatterns' => [
        ]
    ], // SubjectController
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/student'],
        'pluralize'     => false,
        'tokens' => [
            '{id}' => '<id:\d+>',
        ],
        'extraPatterns' => [
        ]
    ] // StudentController
];