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
            'Excel' => Maatwebsite\Excel\Facades\Excel::class,
            'verify_leads_api' => \App\Http\Middleware\VerifyLeadsApiKey::class,
            'PDF' => Barryvdh\DomPDF\Facade::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            '/api/contact-save', // <-- exclude this route
            '/webhook-endpoint/*', // <-- exclude this route
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Exception $e) {
            if ($e->getPrevious() instanceof \Illuminate\Session\TokenMismatchException) {
                return redirect()->route('login')->with('message',"Sesi anda telah habis silahkan login kembali"); 
            };
        });
    })->create();
