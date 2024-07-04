<?php

namespace App\Providers;

use App\Actions\Fortify\RegisterResponse;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthenticatedController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });

        Fortify::verifyEmailView(function() {
            return view('auth.verify-email');
        });

        Fortify::authenticateUsing(function (Request $request) {

            $user = User::where('email', $request->email)->first();

            if (!$user) {

                return null;
            }

            if (!Hash::check($request->password, $user->password)) {

                return null;
            }

            return $user;
        });
    }

    public function register(): void
    {
        $this->app->singleton(
            RegisteredUserController::class,
            RegisterController::class
        );

        $this->app->singleton(
            FortifyLoginRequest::class,
            LoginRequest::class
        );

        $this->app->singleton(
            AuthenticatedSessionController::class,
            AuthenticatedController::class
        );

        $this->app->singleton(
            RegisterResponseContract::class, RegisterResponse::class
        );
    }
}
