<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // 解析请求语言并设置应用语言
        $locale = $this->resolveLocale($request);
        app()->setLocale($locale);

        $response = $next($request);

        // 响应头声明实际语言，方便 API 调用方确认
        $response->headers->set('Content-Language', $locale);

        $vary = $response->headers->get('Vary');
        if ($vary === null || ! str_contains($vary, 'Accept-Language')) {
            $response->headers->set('Vary', $vary ? $vary.', Accept-Language' : 'Accept-Language');
        }

        return $response;
    }

    private function resolveLocale(Request $request): string
    {
        // 优先级：查询参数 > 会话 > Cookie > Accept-Language > 配置回退
        $candidates = [
            $request->query('locale'),
            $request->hasSession() ? $request->session()->get('locale') : null,
            $request->cookie('locale'),
            ...$request->getLanguages(),
        ];

        foreach ($candidates as $candidate) {
            $locale = $this->normalizeLocale($candidate);

            if ($locale !== null) {
                return $locale;
            }
        }

        return $this->fallbackLocale();
    }

    private function normalizeLocale(mixed $candidate): ?string
    {
        if (! is_string($candidate) || trim($candidate) === '') {
            return null;
        }

        // 兼容 BCP 47（zh-TW）与下划线（zh_TW）两种写法
        $normalized = str_replace('-', '_', trim($candidate));
        $lower = strtolower($normalized);
        $supported = $this->supportedLocales();

        // 中文繁体：zh_TW、zh_HK、zh_MO、zh_Hant 系统一律归一为 zh_TW
        if (in_array($lower, ['zh_tw', 'zh_hk', 'zh_mo', 'zh_hant', 'zh_hant_tw', 'zh_hant_hk', 'zh_hant_mo'], true)) {
            return in_array('zh_TW', $supported, true) ? 'zh_TW' : null;
        }

        // 中文简体：zh、zh_CN、zh_Hans 系统一律归一为 zh_CN
        if (in_array($lower, ['zh', 'zh_cn', 'zh_hans', 'zh_hans_cn'], true)) {
            return in_array('zh_CN', $supported, true) ? 'zh_CN' : null;
        }

        // 英文：en 及 en_US、en_GB 等区域变体统一归一为 en
        if ($lower === 'en' || str_starts_with($lower, 'en_')) {
            return in_array('en', $supported, true) ? 'en' : null;
        }

        // 其余按白名单精确匹配（忽略大小写差异）
        foreach ($supported as $locale) {
            if (strtolower($locale) === $lower) {
                return $locale;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function supportedLocales(): array
    {
        // 以配置文件为准，避免与后台切换器硬编码漂移
        $locales = config('app.supported_locales', ['zh_CN', 'zh_TW', 'en']);

        return is_array($locales) && $locales !== [] ? array_values($locales) : ['zh_CN', 'zh_TW', 'en'];
    }

    private function fallbackLocale(): string
    {
        // 使用配置回退语言，不再硬编码
        $fallback = config('app.fallback_locale', config('app.locale', 'zh_CN'));

        if (is_string($fallback) && $fallback !== '') {
            return $fallback;
        }

        return 'zh_CN';
    }
}
