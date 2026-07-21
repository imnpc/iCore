<?php

namespace App\Http\Controllers;

use App\Traits\MakesApiResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * 应用基础控制器。
 */
abstract class Controller
{
    use AuthorizesRequests; // 权限验证
    use MakesApiResponses; // API 响应统一处理
    use ValidatesRequests; // 表单验证
}
