<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Flash;
use App\Http\Redirect;
use App\Http\View;
use App\Security\Auth;
use App\Security\Csrf;

final class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            Redirect::to('/');
        }
        View::render('auth/login', [], 'Sign in');
    }

    public function login(): void
    {
        if (!Csrf::verify(Csrf::fromRequest())) {
            Flash::set('danger', 'Your session expired. Please try again.');
            Redirect::to('/login');
        }

        $email = isset($_POST['email']) ? trim((string) $_POST['email']) : '';
        $password = isset($_POST['password']) ? (string) $_POST['password'] : '';

        if ($email === '' || $password === '' || !Auth::attempt($email, $password)) {
            Flash::set('danger', 'Those details do not match our records.');
            Redirect::to('/login');
        }

        Redirect::to('/');
    }

    public function logout(): void
    {
        if (!Csrf::verify(Csrf::fromRequest())) {
            Flash::set('danger', 'Your session expired. Please try again.');
            Redirect::to('/');
        }
        Auth::logout();
        Redirect::to('/login');
    }
}
