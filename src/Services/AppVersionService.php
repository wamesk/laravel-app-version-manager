<?php

namespace Wame\LaravelAppVersionManager\Services;

use Wame\LaravelAppVersionManager\Exceptions\OlderAppVersion;
use Wame\LaravelAppVersionManager\Models\AppVersion;

class AppVersionService
{
    /**
     * @throws OlderAppVersion
     */
    public function checkVersion(
        ?string $userAppVersion,
    ): void {
        if (isset($userAppVersion)) {
            /** @var AppVersion $appVersion */
            $appVersion = AppVersion::query()
                ->where(['title' => $userAppVersion])
                ->first();

            if (isset($appVersion) && $appVersion->older) {
                throw new OlderAppVersion();
            }
        }
    }
}
