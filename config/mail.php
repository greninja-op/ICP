<?php

return [
    'default' => getenv('MAIL_MAILER') ?: 'smtp',
    
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => getenv('MAIL_HOST') ?: 'smtp.mailtrap.io',
            'port' => (int) (getenv('MAIL_PORT') ?: 2525),
            'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
            'username' => getenv('MAIL_USERNAME'),
            'password' => getenv('MAIL_PASSWORD'),
            'timeout' => null,
        ],
    ],
    
    'from' => [
        'address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@university.edu',
        'name' => getenv('MAIL_FROM_NAME') ?: getenv('APP_NAME') ?: 'University Portal',
    ],
];
