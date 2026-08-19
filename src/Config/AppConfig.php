<?php

declare(strict_types=1);

namespace App\Config;

final class AppConfig
{
    private static string $root = '';

    public static function load(string $root): void
    {
        self::$root = $root;
    }

    public static function root(): string
    {
        return self::$root;
    }

    public static function debug(): bool
    {
        return (getenv('APP_DEBUG') ?: '1') === '1';
    }

    public static function db(): array
    {
        return [
            'host' => getenv('DB_HOST') ?: '127.0.0.1',
            'port' => getenv('DB_PORT') ?: '3306',
            'name' => getenv('DB_NAME') ?: 'fleet_cms',
            'user' => getenv('DB_USER') ?: 'root',
            'pass' => getenv('DB_PASS') ?: '',
        ];
    }
}
