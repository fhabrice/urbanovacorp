<?php
/**
 * Application Configuration
 */

return [
    // Application
    'app' => [
        'name' => 'URBANOVA SOLUTIONS',
        'env' => getenv('APP_ENV') ?: 'production',
        'debug' => getenv('APP_DEBUG') ?: false,
        'url' => getenv('APP_URL') ?: 'https://urbanovacorp.com',
        'timezone' => 'Africa/Kinshasa',
    ],

    // Database
    'database' => [
        'host'      => getenv('DB_HOST') ?: 'localhost',
        'port'      => getenv('DB_PORT') ?: '3306',
        'database'  => getenv('DB_NAME') ?: 'wqmetrvw_urbanova',
        'username'  => getenv('DB_USER') ?: 'wqmetrvw_urbanova',
        'password'  => getenv('DB_PASSWORD') ?: 'Goma@2019',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    // Security
    'security' => [
        'session_name' => 'urbanova_session',
        'session_lifetime' => 7200,
        'csrf_token_name' => 'csrf_token',
        'password_min_length' => 8,
        'max_login_attempts' => 5,
        'lockout_time' => 900,
        'admin_password' => getenv('ADMIN_PASSWORD') ?: 'urbanova',
    ],

    // Upload
    'upload' => [
        'max_size' => 5242880, // 5MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
        'path' => PUBLIC_PATH . '/uploads',
    ],

    // Pagination
    'pagination' => [
        'per_page' => 12,
        'max_per_page' => 50,
    ],

    // Languages
    'languages' => [
        'default' => 'fr',
        'available' => ['fr', 'en'],
        'supported' => [
            'fr' => 'Français',
            'en' => 'English',
        ],
    ],

    // Email
    'email' => [
        'from' => getenv('EMAIL_FROM') ?: 'contact@urbanova.cd',
        'from_name' => 'URBANOVA SOLUTIONS',
        'smtp_host' => getenv('SMTP_HOST') ?: 'localhost',
        'smtp_port' => getenv('SMTP_PORT') ?: 587,
        'smtp_user' => getenv('SMTP_USER') ?: '',
        'smtp_pass' => getenv('SMTP_PASS') ?: '',
    ],
];
