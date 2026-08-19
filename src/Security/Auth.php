<?php

declare(strict_types=1);

namespace App\Security;

use App\Database\Connection;

final class Auth
{
    public static function boot(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function attempt(string $email, string $password): bool
    {
        $stmt = Connection::get()->prepare(
            'SELECT id, name, email, password_hash, role, level, level_id
             FROM users
             WHERE email = ?
             LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        unset($user['password_hash']);
        $_SESSION['user'] = $user;

        return true;
    }

    public static function user(): ?array
    {
        return isset($_SESSION['user']) && is_array($_SESSION['user']) ? $_SESSION['user'] : null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
    }

    /**
     * Tenant identity always comes from the session, never from the request.
     */
    public static function tenant(): array
    {
        $user = self::user();
        if ($user === null) {
            throw new \RuntimeException('Not authenticated.');
        }

        return [
            'level' => (string) $user['level'],
            'level_id' => (int) $user['level_id'],
        ];
    }
}
