<?php

declare(strict_types = 1);

namespace Wame\LaravelAppVersionManager\Observers;

use Illuminate\Http\Request;
use Wame\LaravelAppVersionManager\Enums\VersionStatus;
use Wame\LaravelAppVersionManager\Events\AppVersionUpdatedEvent;
use Wame\LaravelAppVersionManager\Models\AppVersion;

class AppVersionObserver
{
    public function __construct(
        public Request $request,
    ) {
    }

    public function creating(AppVersion $appVersion): void
    {
        if ($this->request->filled(key: 'status_db')) {
            $appVersion->status = VersionStatus::fromDB($this->request->get(key: 'status_db'));
            unset($appVersion->status_db);
        }
    }

    public function updating(AppVersion $appVersion): void
    {
        if ($this->request->filled(key: 'status_db')) {
            $appVersion->status = VersionStatus::fromDB($this->request->get(key: 'status_db'));
            unset($appVersion->status_db);
        }
    }

    public function updated(AppVersion $appVersion): void
    {
        AppVersionUpdatedEvent::dispatch($appVersion);
    }
}
