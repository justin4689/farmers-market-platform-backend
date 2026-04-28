<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsOperatorOrAbove
{
    private const ALLOWED_ROLES = ['admin', 'supervisor', 'operator'];

    public function handle(Request $request, Closure $next): Response
    {
        if (!in_array($request->user()?->role, self::ALLOWED_ROLES, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Insufficient permissions.',
                'errors'  => [],
            ], 403);
        }

        return $next($request);
    }
}
