<?php

namespace App\Http\Middleware;

use App\Models\AppLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequestMiddleware
{

    protected array $except = [
        'scanstat',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if current request matches excluded patterns
        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return $next($request); // just continue without logging
            }
        }

        $response = $next($request);

        /*$start = microtime(true);
        $duration = round((microtime(true) - $start) * 1000, 2);
        try {
            app_log(
                'RouteAccess',
                $request->method() . ' ' . $request->path(),
                $response->getStatusCode(),
                [
                    'duration_ms' => $duration,
                ]
            );
        } catch (\Exception $e) {
            Log::error('app_log failed: ' . $e->getMessage());
        }*/
        return $response;
    }
}
