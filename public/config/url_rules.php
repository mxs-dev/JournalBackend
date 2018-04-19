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
            '{id}'        => '<id:\d+>',
            '{teacherId}' => '<teacherId:\d+>',
            '{subjectId}' => '<subjectId:\d+>'
        ],
        'extraPatterns' => [
            '{teacherId}/assign-subject/{subjectId}'   => 'add-assigned-subject',
            '{teacherId}/unassign-subject/{subjectId}' => 'remove-assigned-subject',
        ]
    ], // TeacherController
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/subject'],
        'pluralize'     => false,
        'tokens' => [
            '{id}'        => '<id:\d+>',
            '{parentId}'  => '<parentId:\d+>',
            '{studentId}' => '<studentId:\d+>'
        ],
        'extraPatterns' => [
            '{parentId}/add-student/{studentId}' => 'add-student',
            '{parentId}/rm-student/{studentId}'  => 'remove-student',
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
    ], // StudentController
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/academic-year'],
        'pluralize'     => false,
        'tokens' => [
            '{id}' => '<id:\d+>',
        ],
        'extraPatterns' => [
        ]
    ], // AcademicYearController
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/semester'],
        'pluralize'     => false,
        'tokens' => [
            '{id}' => '<id:\d+>',
        ],
        'extraPatterns' => [
        ]
    ] // SemesterController
];