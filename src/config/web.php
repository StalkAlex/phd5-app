<?php

/**
 * @link http://www.diemeisterei.de/
 *
 * @copyright Copyright (c) 2016 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Settings for web-application only
return [
    'on registerMenuItems' => function ($event) {
        Yii::$app->params['context.menuItems'] = \yii\helpers\ArrayHelper::merge(
            Yii::$app->params['context.menuItems'],
            $event->sender->getMenuItems()
        );
        $event->handled = true;
    },
    'components' => [
        'errorHandler' => [
            'errorAction' => 'error/index',
        ],
        'log' => [
            'targets' => [
                // writes to php-fpm output stream
                [
                    'class' => 'codemix\streamlog\Target',
                    'url' => 'php://stdout',
                    'levels' => ['info', 'trace'],
                    'logVars' => [],
                    'enabled' => YII_DEBUG && !YII_ENV_TEST,
                ],
                [
                    'class' => 'codemix\streamlog\Target',
                    'url' => 'php://stderr',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                ],
            ],
        ],
        'request' => [
            'cookieValidationKey' => getenv('APP_COOKIE_VALIDATION_KEY'),
        ],
    ],
    'modules' => [
        'docs' => [
            'class' => 'schmunk42\markdocs\Module',
            'layout' => '@backend/views/layouts/box',
        ],
        'settings' => [
            'class' => 'pheme\settings\Module',
            'layout' => '@backend/views/layouts/box',
            'accessRoles' => ['settings-module'],
        ],
    ],
];
