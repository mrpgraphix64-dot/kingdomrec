<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Support both variadic arguments and comma-separated strings
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', (string) $role) as $r) {
                $trimmed = trim($r);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        if (! in_array($request->user()->role, $allowedRoles)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }

            // Redirect safely according to user role
            if ($request->user()->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Access restricted.');
            }
            if ($request->user()->role === 'partner') {
                return redirect()->route('partner.dashboard')->with('error', 'Access restricted.');
            }

            return redirect()->route('portal')->with('error', 'Access restricted.');
        }

        return $next($request);
    }
}
