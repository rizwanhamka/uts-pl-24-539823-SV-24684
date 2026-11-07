<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $id = (string) Str::uuid();

        // Semua log selama request ini otomatis membawa konteks berikut
        Log::withContext([
            'request_id' => $id,
            'method' => $request->method(),
            'path' => $request->path(),
        ]);

        $response = $next($request);
        $response->headers->set('X-Request-Id', $id);

        return $response;
    }
}
