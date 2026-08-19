<?php

declare(strict_types=1);

namespace App\Security;

final class Gate
{
    public static function allows(string $ability, ?array $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        $role = (string) $user['role'];

        return match ($ability) {
            'vehicles.view', 'inspections.view' => in_array($role, ['admin', 'manager', 'driver'], true),
            'vehicles.manage' => $role === 'admin',
            'inspections.manage' => in_array($role, ['admin', 'manager'], true),
            default => false,
        };
    }

    public static function denyUnless(string $ability): void
    {
        if (!self::allows($ability)) {
            http_response_code(403);
            \App\Http\View::render('errors/403', [], 'Forbidden');
            exit;
        }
    }
}
