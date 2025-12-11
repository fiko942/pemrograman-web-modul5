<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiRequestLogger
{
    /**
     * Log setiap request API dan sisipkan header X-Request-ID.
     *
     * @param Request $request permintaan yang sedang diproses.
     * @param Closure(Request): \Symfony\Component\HttpFoundation\Response $next middleware berikutnya.
     */
    public function handle(Request $request, Closure $next)
    {
        $requestId = (string) Str::uuid();
        $startedAt = microtime(true);

        $response = $next($request);

        $durationMs = (int) ((microtime(true) - $startedAt) * 1000);

        Log::channel('api')->info('API request processed', [
            'request_id' => $requestId,
            'method' => $request->method(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $durationMs,
            'user_agent' => $request->userAgent(),
            'query' => $request->query(),
        ]);

        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }
}
