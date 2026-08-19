<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Flash;
use App\Http\Redirect;
use App\Http\View;
use App\Repositories\InspectionRepository;
use App\Repositories\VehicleRepository;
use App\Security\Auth;
use App\Security\Csrf;
use App\Security\Gate;
use App\Validation\Validator;

final class InspectionController
{
    private InspectionRepository $inspections;
    private VehicleRepository $vehicles;

    public function __construct()
    {
        $this->inspections = new InspectionRepository();
        $this->vehicles = new VehicleRepository();
    }

    public function index(): void
    {
        Gate::denyUnless('inspections.view');
        $tenant = Auth::tenant();
        View::render('inspections/index', [
            'inspections' => $this->inspections->all($tenant['level'], $tenant['level_id']),
            'canManage' => Gate::allows('inspections.manage'),
        ], 'Inspections');
    }

    public function create(): void
    {
        Gate::denyUnless('inspections.manage');
        $tenant = Auth::tenant();
        $prefillVehicle = isset($_GET['vehicle_id']) ? (int) $_GET['vehicle_id'] : 0;
        View::render('inspections/form', [
            'inspection' => $this->blank($prefillVehicle),
            'vehicles' => $this->vehicles->all($tenant['level'], $tenant['level_id']),
            'errors' => [],
            'mode' => 'create',
        ], 'Add inspection');
    }

    public function store(): void
    {
        Gate::denyUnless('inspections.manage');
        $this->assertCsrf();
        $tenant = Auth::tenant();
        $user = Auth::user();
        $parsed = $this->validate();

        if ($parsed['errors']) {
            View::render('inspections/form', [
                'inspection' => $parsed['data'],
                'vehicles' => $this->vehicles->all($tenant['level'], $tenant['level_id']),
                'errors' => $parsed['errors'],
                'mode' => 'create',
            ], 'Add inspection');
            return;
        }

        $this->inspections->create($parsed['data'], $tenant['level'], $tenant['level_id'], (int) $user['id']);
        Flash::set('success', 'Inspection saved.');
        Redirect::to('/inspections');
    }

    public function edit(string $id): void
    {
        Gate::denyUnless('inspections.manage');
        $inspection = $this->requireInspection((int) $id);
        $tenant = Auth::tenant();
        View::render('inspections/form', [
            'inspection' => $inspection,
            'vehicles' => $this->vehicles->all($tenant['level'], $tenant['level_id']),
            'errors' => [],
            'mode' => 'edit',
        ], 'Edit inspection');
    }

    public function update(string $id): void
    {
        Gate::denyUnless('inspections.manage');
        $this->assertCsrf();
        $inspection = $this->requireInspection((int) $id);
        $tenant = Auth::tenant();
        $parsed = $this->validate();

        if ($parsed['errors']) {
            View::render('inspections/form', [
                'inspection' => array_merge($inspection, $parsed['data']),
                'vehicles' => $this->vehicles->all($tenant['level'], $tenant['level_id']),
                'errors' => $parsed['errors'],
                'mode' => 'edit',
            ], 'Edit inspection');
            return;
        }

        $this->inspections->update((int) $inspection['id'], $parsed['data'], $tenant['level'], $tenant['level_id']);
        Flash::set('success', 'Inspection updated.');
        Redirect::to('/inspections');
    }

    public function destroy(string $id): void
    {
        Gate::denyUnless('inspections.manage');
        $this->assertCsrf();
        $inspection = $this->requireInspection((int) $id);
        $tenant = Auth::tenant();
        $this->inspections->delete((int) $inspection['id'], $tenant['level'], $tenant['level_id']);
        Flash::set('success', 'Inspection deleted.');
        Redirect::to('/inspections');
    }

    private function requireInspection(int $id): array
    {
        $tenant = Auth::tenant();
        $row = $this->inspections->find($id, $tenant['level'], $tenant['level_id']);
        if ($row === null) {
            http_response_code(404);
            View::render('errors/404', [], 'Not found');
            exit;
        }
        return $row;
    }

    private function validate(): array
    {
        $tenant = Auth::tenant();
        $v = new Validator();
        $vehicleId = $v->intRange('vehicle_id', $_POST['vehicle_id'] ?? null, 1, PHP_INT_MAX);
        $data = [
            'vehicle_id' => $vehicleId,
            'inspection_date' => $v->date('inspection_date', $_POST['inspection_date'] ?? null, true),
            'mileage' => $v->intRange('mileage', $_POST['mileage'] ?? null, 0, 2000000),
            'damage_reported' => $v->bool('damage_reported', $_POST['damage_reported'] ?? null),
            'notes' => is_string($_POST['notes'] ?? null) ? trim($_POST['notes']) : '',
            'status' => $v->inList('status', $_POST['status'] ?? null, ['pending', 'pass', 'fail']),
        ];

        if ($vehicleId && !$this->vehicles->find($vehicleId, $tenant['level'], $tenant['level_id'])) {
            $errors = $v->errors();
            $errors['vehicle_id'] = 'Choose a vehicle from your fleet.';
            return ['data' => $data, 'errors' => $errors];
        }

        if (strlen((string) $data['notes']) > 5000) {
            $errors = $v->errors();
            $errors['notes'] = 'Notes are too long.';
            return ['data' => $data, 'errors' => $errors];
        }

        return ['data' => $data, 'errors' => $v->errors()];
    }

    private function blank(int $vehicleId): array
    {
        return [
            'vehicle_id' => $vehicleId ?: '',
            'inspection_date' => date('Y-m-d'),
            'mileage' => 0,
            'damage_reported' => 0,
            'notes' => '',
            'status' => 'pending',
        ];
    }

    private function assertCsrf(): void
    {
        if (!Csrf::verify(Csrf::fromRequest())) {
            Flash::set('danger', 'Your session expired. Please try again.');
            Redirect::back('/inspections');
        }
    }
}
