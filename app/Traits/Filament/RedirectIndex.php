<?php

namespace App\Traits\Filament;

trait RedirectIndex
{
    /**
     * Filament 重定向到列表页
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
