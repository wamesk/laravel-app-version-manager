<?php

declare(strict_types=1);

namespace Wame\LaravelAppVersionManager\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Wame\LaravelAppVersionManager\Enums\Platform;
use Wame\LaravelAppVersionManager\Enums\VersionStatus;

/**
 * @property string id
 * @property string title
 * @property VersionStatus status
 * @property string status_db
 * @property bool deprecated
 * @property bool older
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class AppVersion extends Model
{
    use HasUlids;

    protected $fillable = [
        'title',
        'status',
        'platform',
    ];

    protected $appends = [
        'status',
        'status_db',
        'deprecated',
        'older',
    ];

    protected $casts = [
        'platform' => Platform::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function allStatuses(): array
    {
        return [
            VersionStatus::CURRENT?->toDB() => VersionStatus::CURRENT->title(),
            VersionStatus::OLDER?->toDB() => VersionStatus::OLDER->title(),
            VersionStatus::DEPRECATED?->toDB() => VersionStatus::DEPRECATED->title(),
        ];
    }

    public function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => VersionStatus::fromDB($value),
            set: fn (VersionStatus $status) => $status?->toDB(),
        );
    }

    public function statusDb(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status_db ?? $this->status?->toDB(),
        );
    }

    public function deprecated(): Attribute
    {
        return Attribute::get(fn () => $this->status === VersionStatus::DEPRECATED);
    }

    public function older(): Attribute
    {
        return Attribute::get(fn () => $this->status === VersionStatus::OLDER);
    }
}
