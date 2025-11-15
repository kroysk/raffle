<?php

namespace App\Config;

class Config {
    public static function get(string $key, $default = null) : ?string
    {
        return getenv($key) ?: $default;
    }

    public static function all() : array
    {
        return [
            'app' => [
                'name' => self::get('APP_NAME', 'ShopWire Raffle'),
                'env' => self::get('APP_ENV', 'development'),
                'debug' => self::get('APP_DEBUG', 'true') === 'true',
                'url' => self::get('APP_URL', 'http://localhost:8080'),
            ],
            'database' => [
                'host' => self::get('DB_HOST', 'db'),
                'port' => self::get('DB_PORT', '5432'),
                'database' => self::get('DB_DATABASE', 'shopwire'),
                'username' => self::get('DB_USERNAME', 'shopwire_user'),
                'password' => self::get('DB_PASSWORD', 'shopwire_password'),
            ],
            'jwt' => [
                'secret' => self::get('JWT_SECRET', 'your-secret-key-change-in-production'),
                'expiration' => (int) self::get('JWT_EXPIRATION', 86400), // 24 hours
            ],
        ];
    }
}
