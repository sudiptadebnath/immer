<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeRequest extends Controller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sanitized = collect($request->all())->map(function ($value) {
            if (is_string($value)) {
                return strip_tags($value);
            }
            return $value;
        })->toArray();
        $request->merge($sanitized);
        return $next($request);
    }
}
