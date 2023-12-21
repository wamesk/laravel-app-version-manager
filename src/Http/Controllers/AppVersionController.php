<?php

namespace Wame\LaravelAppVersionManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Wame\LaravelAppVersionManager\Exceptions\OlderAppVersion;
use Wame\LaravelAppVersionManager\Http\Requests\CheckAppVersionRequest;
use Wame\LaravelAppVersionManager\Services\AppVersionService;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;

class AppVersionController extends Controller
{
    public function check(AppVersionService $service, CheckAppVersionRequest $request): Application|Response|ContractsApplication|ResponseFactory
    {
        try {
            $service->checkVersion(
                userAppVersion: $request->header(key: 'app-version'),
            );

            return response(
                content: [
                    'message' => __(
                        key: 'laravel-app-version-manager::version-messages.up_to_date_app_version.message',
                        replace: [
                            'appName' => config(key: 'laravel-app-version-manager.app_name'),
                        ],
                    ),
                    'update' => false,
                ],
                status: 200,
            );
        } catch (OlderAppVersion) {
            return response(
                content: [
                    'message' => __(
                        key: 'laravel-app-version-manager::version-messages.older_app_version.message',
                        replace: [
                            'appName' => config(key: 'laravel-app-version-manager.app_name'),
                        ],
                    ),
                    'update' => true,
                ],
                status: 200,
            );
        } catch (Exception) {
            return response(
                content: [
                    'message' => __(key: 'Server Error'),
                ],
                status: 500,
            );
        }
    }
}
