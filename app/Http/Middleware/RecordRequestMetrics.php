<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Prometheus\CollectorRegistry;

class RecordRequestMetrics
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);
        $response = $next($request);
        $duration = microtime(true) - $start;

        app(CollectorRegistry::class)
            ->getOrRegisterHistogram(
                'app', 'http_request_duration_seconds', 'HTTP request latency',
                ['method', 'route', 'status'],
                [0.05, 0.1, 0.25, 0.5, 1, 2.5, 5]
            )
            ->observe($duration, [
                $request->method(),
                $request->route()?->uri() ?? 'unknown',
                (string) $response->getStatusCode(),
            ]);
        return $response;
    }
}
