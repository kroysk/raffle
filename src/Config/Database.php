<?php

namespace App\Config;

use PDO;
use PDOException;
use App\Config\Config;
class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                // Heroku provides DATABASE_URL
                $databaseUrl = Config::get('DATABASE_URL');
                
                if ($databaseUrl) {
                    // Parse Heroku DATABASE_URL
                    $url = parse_url($databaseUrl);
                    $host = $url['host'];
                    $port = $url['port'];
                    $dbname = ltrim($url['path'], '/');
                    $user = $url['user'];
                    $password = $url['pass'];
                    $sslmode = 'require';
                } else {
                    // Local development
                    $host = Config::get('DB_HOST') ?: 'db';
                    $port = Config::get('DB_PORT') ?: '5432';
                    $dbname = Config::get('DB_DATABASE') ?: 'shopwire';
                    $user = Config::get('DB_USERNAME') ?: 'shopwire_user';
                    $password = Config::get('DB_PASSWORD') ?: 'shopwire_password';
                    $sslmode = 'prefer';
                }

                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=$sslmode";
                
                self::$connection = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new PDOException("Could not connect to database");
            }
        }

        return self::$connection;
    }

    public static function closeConnection(): void
    {
        self::$connection = null;
    }
}

