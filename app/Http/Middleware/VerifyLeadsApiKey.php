<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyLeadsApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = "4jYE6RC6LZz06mbSboyHl0PBTgKMNmvY8TkIV5WHiTi6AQha8Ji8rXqws4sVmh7s";

        $apiKeyIsValid = (
            ! empty($apiKey)
            && $request->header('x-api-key') == $apiKey
        );

        abort_if (! $apiKeyIsValid, 403, 'Access denied');

        return $next($request);
    }
}
