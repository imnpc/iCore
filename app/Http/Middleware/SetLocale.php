<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** @var array<int, string> */
    private const SUPPORTED_LOCALES = ['zh_CN', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);
        app()->setLocale($locale);

        $response = $next($request);
        $response->headers->set('Content-Language', $locale);

        $vary = $response->headers->get('Vary');
        if ($vary === null || ! str_contains($vary, 'Accept-Language')) {
            $response->headers->set('Vary', $vary ? $vary.', Accept-Language' : 'Accept-Language');
        }

        return $response;
    }

    private function resolveLocale(Request $request): string
    {
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

        return 'zh_CN';
    }

    private function normalizeLocale(mixed $candidate): ?string
    {
        if (! is_string($candidate) || trim($candidate) === '') {
            return null;
        }

        $normalized = str_replace('-', '_', trim($candidate));
        $normalized = strtolower($normalized) === 'zh' ? 'zh_CN' : $normalized;

        if (strtolower($normalized) === 'zh_cn') {
            return 'zh_CN';
        }

        if (strtolower($normalized) === 'en' || str_starts_with(strtolower($normalized), 'en_')) {
            return 'en';
        }

        return in_array($normalized, self::SUPPORTED_LOCALES, true) ? $normalized : null;
    }
}
