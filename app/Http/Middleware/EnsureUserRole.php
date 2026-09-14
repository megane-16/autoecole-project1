<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $role = $user?->role;
        $allowed = $role instanceof UserRole
            ? in_array($role->value, $roles, true)
            : in_array((string) $role, $roles, true);

        abort_unless($user && $allowed, 403, 'Vous n\'avez pas accès à cette application.');

        return $next($request);
    }
}
