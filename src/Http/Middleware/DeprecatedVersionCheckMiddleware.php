<?php

declare(strict_types = 1);

namespace Wame\LaravelAppVersionManager\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Wame\LaravelAppVersionManager\Models\AppVersion;
use Illuminate\Foundation\Application as FoundationApplication;

class DeprecatedVersionCheckMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $userAppVersion = request()->header(key: 'app-version');

        if (isset($userAppVersion)) {
            /** @var AppVersion $appVersion */
            $appVersion = AppVersion::query()
                ->where(['title' => $userAppVersion])
                ->first();

            if (isset($appVersion) && $appVersion->deprecated) {
                return response(
                    content: [
                        'message' => __(
                            key: 'laravel-app-version-manager::version-messages.deprecated_app_version.message',
                            replace: [
                                'appName' => config(key: 'laravel-app-version-manager.app_name'),
                            ],
                        ),
                        'code' => 'app_version_update_required',
                    ],
                    status: 426,
                );
            }
        }

        return $next($request);
    }
}
