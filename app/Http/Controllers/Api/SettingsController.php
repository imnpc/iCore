<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Group('设置', description: '系统设置项', weight: 99)]
#[Prefix('settings')]
#[Middleware(['auth:sanctum'])]
class SettingsController extends Controller
{
    /**
     * 返回所有设置项
     *
     * 输入星号获取所有设置或者指定key获取包含的设置项：app,price,wechat，alipay
     *
     * @param Request $request
     * @return mixed
     */
    #[Get('allSettings')]
    public function allSettings(Request $request)
    {
        cache()->forget(config('settings.cache_key')); // 强制清除设置缓存
        $request->validate([
            /**
             * 设置项查询key，输入星号获取所有设置或者指定key：app,price,website
             * @example app
             */
            'key' => 'required|string',
        ]);
        $data = setting($request->key);

        return $this->success($data);
    }
}
