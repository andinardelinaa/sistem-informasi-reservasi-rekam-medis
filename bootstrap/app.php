<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;           // ← tambahkan ini
use Spatie\Permission\Middleware\PermissionMiddleware;     // ← optional, kalau pakai permission
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware; // ← optional

return Application::configure(basePath: dirname(__DIR__))
        ->withKernels()
        ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    ) //mendaftarkan middleware 
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,                    
            'permission' => PermissionMiddleware::class,        
            'role_or_permission' => RoleOrPermissionMiddleware::class, 
            'CekRole' => \App\Http\Middleware\CekRole::class,
        ]);

        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();