<?php

return [
    'class'      => 'yii\db\Connection',
    'dsn'        => 'mysql:host=localhost;dbname=xchange',
    'username'   => 'xchangeuser',
    'password'   => '12344321',
    'charset'    => 'utf8',
    'attributes' => [
        PDO::ATTR_AUTOCOMMIT => true,
    ],
];
