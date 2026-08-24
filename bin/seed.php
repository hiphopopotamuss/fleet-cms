<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use App\Database\Connection;

// Guard FIRST, before touching the database at all.
if (getenv('APP_ENV') !== 'local') {
    fwrite(STDOUT, "Skipping demo seed — not running in local environment.\n");
    exit(0);
}

$demoPassword = getenv('DEMO_SEED_PASSWORD');
if ($demoPassword === false || $demoPassword === '') {
    fwrite(STDERR, "DEMO_SEED_PASSWORD is not set. Refusing to seed with an empty password.\n");
    exit(1);
}

$pdo = Connection::get();
$stmt = $pdo->query("SELECT id FROM users WHERE password_hash = 'pending'");
$ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!$ids) {
    fwrite(STDOUT, "Passwords already hashed.\n");
    exit(0);
}

$hash = password_hash($demoPassword, PASSWORD_DEFAULT);
$update = $pdo->prepare('UPDATE users SET password_hash = ? WHERE password_hash = ?');
$update->execute([$hash, 'pending']);

// Don't print the actual password to logs, even in local dev.
fwrite(STDOUT, 'Hashed passwords for ' . count($ids) . " demo users.\n");