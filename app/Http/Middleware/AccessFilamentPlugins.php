<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessFilamentPlugins
{
    /**
     * 限制后台管理员是否能访问一些插件页面
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        // 主题选择
        $themesPluginClass = 'Hasnayeen\\Themes\\ThemesPlugin';
        if (auth()->user()->can('page_Themes') && class_exists($themesPluginClass)) {
            filament()->getCurrentPanel()->plugins([
                $themesPluginClass::make()
                    ->canViewThemesPage(function () {
                        return true;
                    }),
            ]);
        }

        // 个人信息页
        $breezyPluginClass = 'Jeffgreco13\\FilamentBreezy\\BreezyCore';
        if (auth()->user()->can('page_MyProfilePage') && class_exists($breezyPluginClass)) {
            filament()->getCurrentPanel()->plugins([
                $breezyPluginClass::make()
                    ->myProfile()
                    ->withoutMyProfileComponents([])
                    ->enableTwoFactorAuthentication(), // 个人信息页
            ]);
        }

        return $next($request);
    }
}
