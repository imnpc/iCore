<?php

namespace App\Http\Middleware;

use Closure;
use Cog\Contracts\Ban\Bannable as BannableContract;
use Illuminate\Http\Request;

/**
 * 封禁用户禁止访问
 */
class ForbidBannedUser
{
    /**
     * Handle an incoming request.
     *
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = auth('sanctum')->user()
            ?? auth('admin')->user()
            ?? auth()->user();

        if ($user && $user instanceof BannableContract && $user->isBanned()) {
            abort(403, __('api.errors.account_banned'));
        }

        return $next($request);
    }
}
