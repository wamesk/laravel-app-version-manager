<?php

namespace Wame\LaravelAppVersionManager\Observers;

use Wame\LaravelAppVersionManager\Events\AppVersionUpdatedEvent;
use Wame\LaravelAppVersionManager\Models\AppVersion;

class AppVersionObserver
{
    public function updated(AppVersion $appVersion): void
    {
        AppVersionUpdatedEvent::dispatch($appVersion);
    }
}
