<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Connection;

final class VehicleRepository
{
    /**
     * Tenant filters are always bound as parameters. Callers must pass
     * Auth::tenant() — never request input.
     */
    public function all(string $level, int $levelId): array
    {
        $stmt = Connection::get()->prepare(
            'SELECT * FROM vehicles WHERE level = ? AND level_id = ? ORDER BY registration ASC'
        );
        $stmt->execute([$level, $levelId]);
        return $stmt->fetchAll();
    }

    public function find(int $id, string $level, int $levelId): ?array
    {
        $stmt = Connection::get()->prepare(
            'SELECT * FROM vehicles WHERE id = ? AND level = ? AND level_id = ? LIMIT 1'
        );
        $stmt->execute([$id, $level, $levelId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function registrationTaken(string $registration, string $level, int $levelId, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT id FROM vehicles WHERE registration = ? AND level = ? AND level_id = ?';
        $params = [$registration, $level, $levelId];
        if ($ignoreId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $ignoreId;
        }
        $sql .= ' LIMIT 1';
        $stmt = Connection::get()->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetch();
    }

    public function create(array $data, string $level, int $levelId): int
    {
        $stmt = Connection::get()->prepare(
            'INSERT INTO vehicles (level, level_id, registration, make, model, year, mileage, mot_expiry, tax_expiry, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $level,
            $levelId,
            $data['registration'],
            $data['make'],
            $data['model'],
            $data['year'],
            $data['mileage'],
            $data['mot_expiry'],
            $data['tax_expiry'],
            $data['status'],
        ]);
        return (int) Connection::get()->lastInsertId();
    }

    public function update(int $id, array $data, string $level, int $levelId): bool
    {
        $stmt = Connection::get()->prepare(
            'UPDATE vehicles
             SET registration = ?, make = ?, model = ?, year = ?, mileage = ?, mot_expiry = ?, tax_expiry = ?, status = ?
             WHERE id = ? AND level = ? AND level_id = ?'
        );
        $stmt->execute([
            $data['registration'],
            $data['make'],
            $data['model'],
            $data['year'],
            $data['mileage'],
            $data['mot_expiry'],
            $data['tax_expiry'],
            $data['status'],
            $id,
            $level,
            $levelId,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id, string $level, int $levelId): bool
    {
        $stmt = Connection::get()->prepare(
            'DELETE FROM vehicles WHERE id = ? AND level = ? AND level_id = ?'
        );
        $stmt->execute([$id, $level, $levelId]);
        return $stmt->rowCount() > 0;
    }
}
