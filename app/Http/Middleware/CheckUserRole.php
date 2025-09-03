<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole extends Controller
{
    public function handle(Request $request, Closure $next, $roles = null): Response
    {
        $userRole = getUsrProp('role');
        if (!userLogged() || ($roles && strpos($roles, $userRole) === false)) {
            if ($request->expectsJson()) {
                return $this->err('Unauthorized Access', [], 401);
            }
            abort(403, 'You are not authorized to access this page');
        }
        return $next($request);
    }
}
