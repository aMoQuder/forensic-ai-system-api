<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rules\In;
use Illuminate\Support\Facades\Auth;
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        if (strtolower($user->role) === 'admin') {
            return $next($request);
        }

        if (!in_array(strtolower($user->role), array_map('strtolower', $roles))) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
