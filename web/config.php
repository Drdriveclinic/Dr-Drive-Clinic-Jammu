<?php
declare(strict_types=1);

// THE DRIVE CLINIC — Web Configuration
// Loads settings from environment or fallback defaults. Never hardcode credentials.

if (!function_exists('env')) {
    function env(string $key, ?string $default = null): ?string {
        $val = getenv($key);
        if ($val === false || $val === null || $val === '') {
            return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        }
        return $val;
    }
}

return [
    'app_name'      => 'THE DRIVE CLINIC',
    'tagline'       => 'WHERE CARS GET THEIR SHINE BACK',
    'site_url'      => env('SITE_URL', 'http://localhost:8000'),
    'api_base_url'  => env('API_BASE_URL', 'http://localhost:5000/api'),
    'env'           => env('APP_ENV', 'development'),
    
    // Database Credentials
    'db' => [
        'driver'    => env('DB_DRIVER', 'mysql'),
        'host'      => env('DB_HOST', '127.0.0.1'),
        'port'      => (int) env('DB_PORT', '3306'),
        'database'  => env('DB_NAME', 'drive_clinic'),
        'username'  => env('DB_USER', 'root'),
        'password'  => env('DB_PASS', ''),
        'charset'   => 'utf8mb4'
    ],
    
    // Default Studio Info (fallback if DB query is empty)
    'studio' => [
        'phone'         => '+91 94191 00000',
        'whatsapp'      => '919419100000',
        'email'         => 'care@thedriveclinic.com',
        'address'       => 'Nanak Nagar, Jammu, Jammu & Kashmir 180004, India',
        'instagram'     => 'https://instagram.com/thedriveclinic',
        'hours'         => 'Mon - Sun: 09:00 AM - 08:00 PM',
        'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13426.123456789!2d74.871234!3d32.701234!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzLCsDQyJzA0LjQiTiA3NMKwNTInMTYuNCJF!5e0!3m2!1sen!2sin!4v1600000000000!5m2!1sen!2sin'
    ],

    // GoDaddy Business Email Configuration
    'mail' => [
        'to'       => env('NOTIFICATION_EMAIL', 'care@thedriveclinic.com'),
        'from'     => env('NOTIFICATION_FROM', 'care@thedriveclinic.com'),
        'host'     => env('SMTP_HOST', 'smtpout.secureserver.net'),
        'port'     => (int) env('SMTP_PORT', '465'),
        'username' => env('SMTP_USER', 'care@thedriveclinic.com'),
        'password' => env('SMTP_PASS', '')
    ]
];
