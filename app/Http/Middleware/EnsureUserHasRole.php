<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Memastikan pengguna terautentikasi memiliki salah satu role yang diizinkan. */
final class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        abort_unless($request->user()?->role instanceof UserRole && in_array($request->user()->role->value, $roles, true), 403);

        return $next($request);
    }
}
