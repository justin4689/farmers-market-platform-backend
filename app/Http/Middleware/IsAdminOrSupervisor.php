<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdminOrSupervisor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!in_array($request->user()?->role, ['admin', 'supervisor'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Admin or Supervisor access required.',
                'errors'  => [],
            ], 403);
        }

        return $next($request);
    }
}
