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
    use AuthorizesRequests; // 表单验证
    use MakesApiResponses; // policy 验证
    use ValidatesRequests; // API 响应统一处理
}
