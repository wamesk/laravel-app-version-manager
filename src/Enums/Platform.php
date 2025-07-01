<?php

namespace Wame\LaravelAppVersionManager\Enums;

enum Platform: string
{
    case WEB = 'web';
    case ANDROID = 'android';
    case IOS = 'ios';

    public function title(): string
    {
        return (string) __("laravel-app-version-manager::platform.{$this->value}");
    }
}
