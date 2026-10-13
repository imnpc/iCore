<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Group('设置', description: '系统设置项', weight: 99)]
#[Prefix('settings')]
#[Middleware(['auth:sanctum', 'forbid.banned'])]
class SettingsController extends Controller
{
    /**
     * 返回所有设置项
     *
     * 仅允许读取配置中明确声明的公开设置项。
     *
     * @return mixed
     */
    #[Get('allSettings')]
    public function allSettings(Request $request): JsonResponse
    {
        $request->validate([
            /**
             * 设置项查询 key，例如：app
             *
             * @example app
             */
            'key' => [
                'required',
                'string',
                Rule::in(config('settings.api.public_keys', [])),
            ],
        ]);
        $data = setting($request->key);

        return $this->success($data);
    }
}
