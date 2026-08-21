<?php

namespace App\Http\Middleware;

use App\Domain\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $allowed = array_map(fn (string $role) => Role::from($role), $roles);
        if (! in_array($user->role, $allowed, true)) {
            return redirect()->route('login', ['error' => 'role']);
        }

        return $next($request);
    }
}
