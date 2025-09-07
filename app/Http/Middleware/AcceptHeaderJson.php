<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AcceptHeaderJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 强制设置 Accept 请求头为 application/json
        $request->headers->set('Accept', 'application/json');

        // 强制设置请求期望 JSON 响应
        $request->headers->set('Content-Type', 'application/json');

        return $next($request);
    }
}
