<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole extends Controller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!userLogged() || strpos(implode("",$roles), getUsrProp('role')) == false) {
            if ($request->expectsJson()) {
                return $this->err('Unauthorized Access', [], 401);
            }
            abort(403, 'You are not authorized to access this page');
        }
        return $next($request);
    }
}
