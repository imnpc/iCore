<?php

namespace App\Http\Middleware;

use Closure;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Http\Request;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Symfony\Component\HttpFoundation\Response;

class AccessFilamentPlugins
{
    /**
     * 限制后台管理员是否能访问一些插件页面
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        // 主题选择
        if (auth()->user()->can('page_Themes')) {
            filament()->getCurrentPanel()->plugins([
                ThemesPlugin::make()
                    ->canViewThemesPage(function () {
                        return true;
                    }),
            ]);
        }

        // 个人信息页
        if (auth() && auth()->user()->cant('page_MyProfilePage')) {
            filament()->getCurrentPanel()->plugins([
                BreezyCore::make()
                    ->myProfile()
                    ->withoutMyProfileComponents([])
                    ->enableTwoFactorAuthentication(), // 个人信息页
            ]);
        }

        return $next($request);
    }
}
