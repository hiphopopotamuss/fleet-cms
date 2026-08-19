<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\View;
use App\Repositories\InspectionRepository;
use App\Repositories\VehicleRepository;
use App\Security\Auth;

final class DashboardController
{
    public function index(): void
    {
        $tenant = Auth::tenant();
        $vehicles = (new VehicleRepository())->all($tenant['level'], $tenant['level_id']);
        $inspections = (new InspectionRepository())->all($tenant['level'], $tenant['level_id']);

        View::render('dashboard/index', [
            'vehicleCount' => count($vehicles),
            'inspectionCount' => count($inspections),
            'upcomingMot' => array_values(array_filter($vehicles, static function (array $v): bool {
                return !empty($v['mot_expiry']) && $v['mot_expiry'] <= date('Y-m-d', strtotime('+30 days'));
            })),
        ], 'Dashboard');
    }
}
