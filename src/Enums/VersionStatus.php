<?php

namespace Wame\LaravelAppVersionManager\Enums;

enum VersionStatus: string
{
    case CURRENT = 'current';
    case OLDER = 'older';
    case DEPRECATED = 'deprecated';

    public function toDB(): ?string
    {
        return match ($this) {
            self::CURRENT => '1',
            self::OLDER => '2',
            self::DEPRECATED => '3',
            default => null,
        };
    }

    public static function fromDB($value): ?VersionStatus
    {
        return match ($value) {
            '1' => self::CURRENT,
            '2' => self::OLDER,
            '3' => self::DEPRECATED,
            default => null
        };
    }
}

