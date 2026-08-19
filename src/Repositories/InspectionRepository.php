<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Connection;

final class InspectionRepository
{
    public function all(string $level, int $levelId): array
    {
        $stmt = Connection::get()->prepare(
            'SELECT i.*, v.registration
             FROM inspections i
             INNER JOIN vehicles v ON v.id = i.vehicle_id AND v.level = i.level AND v.level_id = i.level_id
             WHERE i.level = ? AND i.level_id = ?
             ORDER BY i.inspection_date DESC, i.id DESC'
        );
        $stmt->execute([$level, $levelId]);
        return $stmt->fetchAll();
    }

    public function forVehicle(int $vehicleId, string $level, int $levelId): array
    {
        $stmt = Connection::get()->prepare(
            'SELECT * FROM inspections
             WHERE vehicle_id = ? AND level = ? AND level_id = ?
             ORDER BY inspection_date DESC, id DESC'
        );
        $stmt->execute([$vehicleId, $level, $levelId]);
        return $stmt->fetchAll();
    }

    public function find(int $id, string $level, int $levelId): ?array
    {
        $stmt = Connection::get()->prepare(
            'SELECT i.*, v.registration
             FROM inspections i
             INNER JOIN vehicles v ON v.id = i.vehicle_id AND v.level = i.level AND v.level_id = i.level_id
             WHERE i.id = ? AND i.level = ? AND i.level_id = ?
             LIMIT 1'
        );
        $stmt->execute([$id, $level, $levelId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data, string $level, int $levelId, int $userId): int
    {
        $stmt = Connection::get()->prepare(
            'INSERT INTO inspections (level, level_id, vehicle_id, inspection_date, mileage, damage_reported, notes, status, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $level,
            $levelId,
            $data['vehicle_id'],
            $data['inspection_date'],
            $data['mileage'],
            $data['damage_reported'],
            $data['notes'],
            $data['status'],
            $userId,
        ]);
        return (int) Connection::get()->lastInsertId();
    }

    public function update(int $id, array $data, string $level, int $levelId): bool
    {
        $stmt = Connection::get()->prepare(
            'UPDATE inspections
             SET vehicle_id = ?, inspection_date = ?, mileage = ?, damage_reported = ?, notes = ?, status = ?
             WHERE id = ? AND level = ? AND level_id = ?'
        );
        $stmt->execute([
            $data['vehicle_id'],
            $data['inspection_date'],
            $data['mileage'],
            $data['damage_reported'],
            $data['notes'],
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
            'DELETE FROM inspections WHERE id = ? AND level = ? AND level_id = ?'
        );
        $stmt->execute([$id, $level, $levelId]);
        return $stmt->rowCount() > 0;
    }
}
