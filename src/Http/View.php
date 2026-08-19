<?php

declare(strict_types=1);

namespace App\Http;

use App\Config\AppConfig;
use App\Security\Auth;
use App\Security\Csrf;

final class View
{
    public static function render(string $template, array $data = [], string $title = 'Fleet CMS'): void
    {
        $data['title'] = $title;
        $data['user'] = Auth::user();
        $data['csrf'] = Csrf::token();
        $data['flash'] = Flash::pull();
        $data['e'] = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

        extract($data, EXTR_SKIP);

        $viewFile = AppConfig::root() . '/views/' . $template . '.php';
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require AppConfig::root() . '/views/layout.php';
    }

    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
