<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\InspectionController;
use App\Controllers\VehicleController;
use App\Http\View;
use App\Security\Auth;
use App\Security\Csrf;

/** @var \Bramus\Router\Router $router */

$router->before('GET|POST', '/.*', static function (): void {
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
});

$router->before('POST', '/.*', static function (): void {
    if (!Csrf::verify(Csrf::fromRequest())) {
        http_response_code(419);
        echo 'Invalid or missing CSRF token.';
        exit;
    }
});

$router->before('GET|POST', '/.*', static function (): void {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    if ($path === '/login') {
        return;
    }
    if (!Auth::check()) {
        header('Location: /login');
        exit;
    }
});

$router->get('/login', static fn () => (new AuthController())->showLogin());
$router->post('/login', static fn () => (new AuthController())->login());
$router->post('/logout', static fn () => (new AuthController())->logout());

$router->get('/', static fn () => (new DashboardController())->index());

$router->get('/vehicles', static fn () => (new VehicleController())->index());
$router->get('/vehicles/create', static fn () => (new VehicleController())->create());
$router->post('/vehicles', static fn () => (new VehicleController())->store());
$router->get('/vehicles/(\d+)', static fn ($id) => (new VehicleController())->show($id));
$router->get('/vehicles/(\d+)/edit', static fn ($id) => (new VehicleController())->edit($id));
$router->post('/vehicles/(\d+)', static fn ($id) => (new VehicleController())->update($id));
$router->post('/vehicles/(\d+)/delete', static fn ($id) => (new VehicleController())->destroy($id));

$router->get('/inspections', static fn () => (new InspectionController())->index());
$router->get('/inspections/create', static fn () => (new InspectionController())->create());
$router->post('/inspections', static fn () => (new InspectionController())->store());
$router->get('/inspections/(\d+)/edit', static fn ($id) => (new InspectionController())->edit($id));
$router->post('/inspections/(\d+)', static fn ($id) => (new InspectionController())->update($id));
$router->post('/inspections/(\d+)/delete', static fn ($id) => (new InspectionController())->destroy($id));

$router->set404(static function (): void {
    http_response_code(404);
    if (Auth::check()) {
        View::render('errors/404', [], 'Not found');
        return;
    }
    echo 'Not found';
});
