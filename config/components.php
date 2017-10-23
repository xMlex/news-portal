<?php

return [
    'db' => $db,
    'cache' => [
        'class' => 'yii\caching\FileCache',
    ],
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
        'cache' => 'cache',
        'itemTable' => '{{%auth_item}}',
        'itemChildTable' => '{{%auth_item_child}}',
        'assignmentTable' => '{{%auth_assignment}}',
        'ruleTable' => '{{%auth_rule}}',
        'defaultRoles' => ['guest'],
    ],
    'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'transport' => array_merge([
            'class' => 'Swift_SmtpTransport',
        ], $params['smtp']),
        'messageConfig' => [
            'charset' => 'UTF-8',
            'from' => ['support@xmlex.ru' => 'Новостной портал'],
        ],
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
];