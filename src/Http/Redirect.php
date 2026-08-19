<?php

declare(strict_types=1);

namespace App\Http;

final class Redirect
{
    public static function to(string $path): never
    {
        header('Location: ' . $path, true, 302);
        exit;
    }

    public static function back(string $fallback = '/'): never
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? $fallback;
        if (!is_string($ref) || $ref === '') {
            $ref = $fallback;
        }
        header('Location: ' . $ref, true, 302);
        exit;
    }
}
