<?php

namespace App\Http\Controllers;

use App\Traits\MakesApiResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use AuthorizesRequests; // policy 验证
    use ValidatesRequests; // 表单验证
    use MakesApiResponses; // API 响应统一处理
}
