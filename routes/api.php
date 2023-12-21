<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Route;
use Wame\LaravelAppVersionManager\Http\Controllers\AppVersionController;

Route::get(uri: 'app-version-check', action: [AppVersionController::class, 'check'])->name(name: 'laravel-app-version-manager.check');
