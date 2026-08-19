<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use App\Database\Connection;

$pdo = Connection::get();
$stmt = $pdo->query("SELECT id FROM users WHERE password_hash = 'pending'");
$ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!$ids) {
    fwrite(STDOUT, "Passwords already hashed.\n");
    exit(0);
}

$hash = password_hash('Password123!', PASSWORD_DEFAULT);
$update = $pdo->prepare('UPDATE users SET password_hash = ? WHERE password_hash = ?');
$update->execute([$hash, 'pending']);

fwrite(STDOUT, 'Hashed passwords for ' . count($ids) . " demo users (Password123!).\n");
