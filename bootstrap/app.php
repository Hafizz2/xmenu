<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'cafe' => \App\Http\Middleware\EnsureCafe::class,
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        // Behind a load balancer / Cloudflare: trust its HTTPS headers.
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: ['e']);
        $middleware->encryptCookies(except: ['xm_lang']);
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo('/dashboard');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
