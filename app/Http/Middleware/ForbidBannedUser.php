<?php

namespace App\Http\Middleware;

use Closure;
use Cog\Contracts\Ban\Bannable as BannableContract;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

/**
 * 封禁用户禁止访问
 */
class ForbidBannedUser
{
    public function __construct(protected Guard $auth) {}

    /**
     * Handle an incoming request.
     *
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $this->auth->user();

        if ($user && $user instanceof BannableContract && $user->isBanned()) {
            abort(403, '您的账号已被禁用，请联系管理员!');
        }

        return $next($request);
    }
}
