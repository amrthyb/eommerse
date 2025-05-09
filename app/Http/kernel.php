<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'api' => [\App\Http\Middleware\SetApiLocale::class],
        'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        // 'permission' => \App\Http\Middleware\PermissionMiddleware::class,
    ];
}
