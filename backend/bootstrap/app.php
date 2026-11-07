<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\RequestContext;
use App\Support\Actor;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(append: [RequestContext::class]);
    })

->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (ValidationException $e, Request $request) {
        Log::warning('Validasi gagal', [
            'actor' => Actor::label($request->user()),
            'action' => $request->route()?->getName() ?? $request->method().' '.$request->path(),
            'reason' => 'validation_failed',
            'failed_fields' => array_keys($e->errors()),   // hanya NAMA field, bukan nilainya
        ]);
        // tidak mengembalikan apa pun: respons 422 tetap dibuat Laravel seperti biasa
    });
    })->create();
