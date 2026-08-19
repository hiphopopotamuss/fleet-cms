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

final class VehicleController
{
    private VehicleRepository $vehicles;
    private InspectionRepository $inspections;

    public function __construct()
    {
        $this->vehicles = new VehicleRepository();
        $this->inspections = new InspectionRepository();
    }

    public function index(): void
    {
        Gate::denyUnless('vehicles.view');
        $tenant = Auth::tenant();
        View::render('vehicles/index', [
            'vehicles' => $this->vehicles->all($tenant['level'], $tenant['level_id']),
            'canManage' => Gate::allows('vehicles.manage'),
        ], 'Vehicles');
    }

    public function show(string $id): void
    {
        Gate::denyUnless('vehicles.view');
        $vehicle = $this->requireVehicle((int) $id);
        $tenant = Auth::tenant();
        View::render('vehicles/show', [
            'vehicle' => $vehicle,
            'inspections' => $this->inspections->forVehicle((int) $vehicle['id'], $tenant['level'], $tenant['level_id']),
            'canManage' => Gate::allows('vehicles.manage'),
            'canInspect' => Gate::allows('inspections.manage'),
        ], $vehicle['registration']);
    }

    public function create(): void
    {
        Gate::denyUnless('vehicles.manage');
        View::render('vehicles/form', [
            'vehicle' => $this->blank(),
            'errors' => [],
            'mode' => 'create',
        ], 'Add vehicle');
    }

    public function store(): void
    {
        Gate::denyUnless('vehicles.manage');
        $this->assertCsrf();
        $tenant = Auth::tenant();
        $parsed = $this->validate(null);

        if ($parsed['errors']) {
            View::render('vehicles/form', [
                'vehicle' => $parsed['data'],
                'errors' => $parsed['errors'],
                'mode' => 'create',
            ], 'Add vehicle');
            return;
        }

        $this->vehicles->create($parsed['data'], $tenant['level'], $tenant['level_id']);
        Flash::set('success', 'Vehicle added.');
        Redirect::to('/vehicles');
    }

    public function edit(string $id): void
    {
        Gate::denyUnless('vehicles.manage');
        $vehicle = $this->requireVehicle((int) $id);
        View::render('vehicles/form', [
            'vehicle' => $vehicle,
            'errors' => [],
            'mode' => 'edit',
        ], 'Edit vehicle');
    }

    public function update(string $id): void
    {
        Gate::denyUnless('vehicles.manage');
        $this->assertCsrf();
        $vehicle = $this->requireVehicle((int) $id);
        $tenant = Auth::tenant();
        $parsed = $this->validate((int) $vehicle['id']);

        if ($parsed['errors']) {
            View::render('vehicles/form', [
                'vehicle' => array_merge($vehicle, $parsed['data']),
                'errors' => $parsed['errors'],
                'mode' => 'edit',
            ], 'Edit vehicle');
            return;
        }

        $this->vehicles->update((int) $vehicle['id'], $parsed['data'], $tenant['level'], $tenant['level_id']);
        Flash::set('success', 'Vehicle updated.');
        Redirect::to('/vehicles/' . $vehicle['id']);
    }

    public function destroy(string $id): void
    {
        Gate::denyUnless('vehicles.manage');
        $this->assertCsrf();
        $vehicle = $this->requireVehicle((int) $id);
        $tenant = Auth::tenant();

        $linked = $this->inspections->forVehicle((int) $vehicle['id'], $tenant['level'], $tenant['level_id']);
        if ($linked) {
            Flash::set('danger', 'Remove inspections for this vehicle before deleting it.');
            Redirect::to('/vehicles/' . $vehicle['id']);
        }

        $this->vehicles->delete((int) $vehicle['id'], $tenant['level'], $tenant['level_id']);
        Flash::set('success', 'Vehicle deleted.');
        Redirect::to('/vehicles');
    }

    private function requireVehicle(int $id): array
    {
        $tenant = Auth::tenant();
        $vehicle = $this->vehicles->find($id, $tenant['level'], $tenant['level_id']);
        if ($vehicle === null) {
            http_response_code(404);
            View::render('errors/404', [], 'Not found');
            exit;
        }
        return $vehicle;
    }

    private function validate(?int $ignoreId): array
    {
        $tenant = Auth::tenant();
        $v = new Validator();
        $data = [
            'registration' => $v->requireString('registration', $_POST['registration'] ?? null, 20),
            'make' => $v->requireString('make', $_POST['make'] ?? null, 80),
            'model' => $v->requireString('model', $_POST['model'] ?? null, 80),
            'year' => $v->intRange('year', $_POST['year'] ?? null, 1980, (int) date('Y') + 1),
            'mileage' => $v->intRange('mileage', $_POST['mileage'] ?? null, 0, 2000000),
            'mot_expiry' => $v->date('mot_expiry', $_POST['mot_expiry'] ?? null),
            'tax_expiry' => $v->date('tax_expiry', $_POST['tax_expiry'] ?? null),
            'status' => $v->inList('status', $_POST['status'] ?? null, ['active', 'inactive', 'maintenance']),
        ];

        if ($data['registration'] && $this->vehicles->registrationTaken($data['registration'], $tenant['level'], $tenant['level_id'], $ignoreId)) {
            $errors = $v->errors();
            $errors['registration'] = 'That registration is already on the fleet.';
            return ['data' => $data, 'errors' => $errors];
        }

        return ['data' => $data, 'errors' => $v->errors()];
    }

    private function blank(): array
    {
        return [
            'registration' => '',
            'make' => '',
            'model' => '',
            'year' => date('Y'),
            'mileage' => 0,
            'mot_expiry' => '',
            'tax_expiry' => '',
            'status' => 'active',
        ];
    }

    private function assertCsrf(): void
    {
        if (!Csrf::verify(Csrf::fromRequest())) {
            Flash::set('danger', 'Your session expired. Please try again.');
            Redirect::back('/vehicles');
        }
    }
}
