<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/../yii2/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
