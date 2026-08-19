<?php

declare(strict_types=1);

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');

require dirname(__DIR__) . '/src/bootstrap.php';

$router = new \Bramus\Router\Router();
require dirname(__DIR__) . '/src/routes.php';
$router->run();
