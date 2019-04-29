<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{

    public function handle($request, Closure $next,...$role)
    {
        $user_role = $request->auth->role;
        if (!in_array($user_role,$role)) {
            return response()->json([
                'error' => 'Unauthorized',
                'role' => $user_role
            ], 403);
        }
        return $next($request);
        
    }
}
