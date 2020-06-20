<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'errorCode' => require(__DIR__ . '/errCode.php'),
    'amqp' => [
        'host' => '127.0.0.1',
        'port' => 5672,         //端口
        'login' => 'test',      //账号
        'password' => '123456', //密码
        'vhost' => '/'          //虚拟主机
    ],
];
