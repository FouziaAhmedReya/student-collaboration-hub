<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        abort_unless(
            in_array($user->role, $roles, true),
            403,
            'You do not have permission to access this feature.'
        );

        if (
            $user->role === 'tutor'
            && $user->status !== 'approved'
        ) {
            abort(
                403,
                'Your tutor account is waiting for administrator approval.'
            );
        }

        return $next($request);
    }
}