<?php

use Illuminate\Http\Request;
use App\Http\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            "authenticate" => Authenticate::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('courier/*')) {
                return route('courier');
            }
            return route('auth.login');
        });
        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->is('courier')) {
                return route('courier.scan');
            }
            return route('welcome');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
