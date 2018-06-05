<?php
$tokens = [
    '{id}'         => '<id:\d+>',
    '{groupId}'    => '<groupId:\d+>',
    '{yearId}'     => '<yearId:\d+>',
    '{teacherId}'  => '<teacherId:\d+>',
    '{subjectId}'  => '<subjectId:\d+>',
    '{studentId}'  => '<studentId:\d+>',
    '{semesterId}' => '<semesterId:\d+>'
];


return [
    'api' => '/v1',
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/user'],
        'pluralize'     => false,
        'tokens' => $tokens,
        'extraPatterns' => []
    ], // UserController
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/group'],
        'pluralize'     => false,
        'tokens' => $tokens,
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
        'tokens' => $tokens,
        'extraPatterns' => [
            '{teacherId}/assign-subject/{subjectId}'   => 'add-assigned-subject',
            '{teacherId}/unassign-subject/{subjectId}' => 'remove-assigned-subject',
            '{teacherId}/teaching-years/' => 'get-teaching-academic-years',
            '{teacherId}/teaches-by-semester/{semesterId}' => 'get-teaches-by-semester',
            '{teacherId}/teaches-by-year/{yearId}' => 'get-teaches-by-academic-year'
        ]
    ], // TeacherController
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/subject'],
        'pluralize'     => false,
        'tokens' => [],
        'extraPatterns' => []
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
        'extraPatterns' => []
    ], // AcademicYearController
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/semester'],
        'pluralize'     => false,
        'tokens' => [
            '{id}' => '<id:\d+>',
        ],
        'extraPatterns' => []
    ], // SemesterController
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/lesson'],
        'pluralize'     => false,
        'tokens' => [
            '{id}' => '<id:\d+>',
        ],
        'extraPatterns' => []
    ], // LessonController
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/teaches'],
        'pluralize'     => false,
        'tokens' => [
            '{id}' => '<id:\d+>',
        ],
        'extraPatterns' => [
            '{id}/calculate-total-grades' => 'calculate-total-grades',
        ]
    ], // TeachesController
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => ['v1/grade'],
        'pluralize'     => false,
        'tokens' => [
            '{id}' => '<id:\d+>',
        ],
        'extraPatterns' => []
    ]  // GradeController
];