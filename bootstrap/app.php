<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn() => route('auth.login'));
        $middleware->redirectUsersTo(fn() => route('home'));
        $middleware->alias([
            'auth'      => Authenticate::class,
            'guest'     => RedirectIfAuthenticated::class,
            'signed'    => ValidateSignature::class,
            'throttle'  => ThrottleRequests::class,
            'verified'  => EnsureEmailIsVerified::class,
            'role'      => \App\Http\Middleware\CheckRole::class,
            'admin'     => \App\Http\Middleware\CheckAdmin::class,
            'moderator' => \App\Http\Middleware\CheckModerator::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
