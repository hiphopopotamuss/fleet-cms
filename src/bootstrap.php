<?php

declare(strict_types=1);

use App\Config\AppConfig;
use App\Database\Connection;
use App\Security\Auth;
use App\Security\Csrf;

$root = dirname(__DIR__);

if (is_file($root . '/vendor/autoload.php')) {
    require $root . '/vendor/autoload.php';
} else {
    spl_autoload_register(static function (string $class) use ($root): void {
        $prefix = 'App\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }
        $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
        $path = $root . '/src/' . $relative . '.php';
        if (is_file($path)) {
            require $path;
        }
    });

    $routerFile = $root . '/vendor/bramus/router/src/Bramus/Router/Router.php';
    if (is_file($routerFile)) {
        require $routerFile;
    }
}

$local = $root . '/config/local.php';
if (is_file($local)) {
    require $local;
}

AppConfig::load($root);
Connection::boot();
Auth::boot();
Csrf::boot();
