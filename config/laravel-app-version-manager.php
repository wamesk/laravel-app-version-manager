<?php

return [
    'app_name' => env(key: 'APP_NAME', default: 'App Name'),

    'route.prefix' => 'api/v1',

    'platforms' => [
        \Wame\LaravelAppVersionManager\Enums\Platform::WEB,
        \Wame\LaravelAppVersionManager\Enums\Platform::ANDROID,
        \Wame\LaravelAppVersionManager\Enums\Platform::IOS,
    ],
];
