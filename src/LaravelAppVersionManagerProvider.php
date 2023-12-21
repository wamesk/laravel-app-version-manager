<?php

declare(strict_types = 1);

namespace Wame\LaravelAppVersionManager;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Wame\LaravelAppVersionManager\Events\AppVersionUpdatedEvent;
use Wame\LaravelAppVersionManager\Http\Middleware\DeprecatedVersionCheckMiddleware;
use Wame\LaravelAppVersionManager\Jobs\SaveAppVersionHistoryJob;
use Wame\LaravelAppVersionManager\Models\AppVersion;
use Wame\LaravelAppVersionManager\Observers\AppVersionObserver;

class LaravelAppVersionManagerProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            // Export the migration
            $this->publishMigrations();

            // Export configs
            $this->publishConfigs();

            // Export views
            //$this->publishViews();

            // Export translations
            $this->publishTranslations();

            // Register Commands
            //$this->commands([
            //    InstallLaravelAuth::class,
            //]);
        }

        AppVersion::observe(classes: AppVersionObserver::class);

        Event::listen(function (AppVersionUpdatedEvent $event): void {
            $appVersion = $event->entity;
            SaveAppVersionHistoryJob::dispatchSync(
                $appVersion,
                $appVersion->getOriginal(key: 'status')->toDB(),
                $appVersion->getAttribute(key: 'status')->toDB(),
            );
        });

        $this->registerRoutes();
        $this->registerTranslations();
    }

    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(
            path: __DIR__ . '/../resources/lang',
            namespace: 'laravel-app-version-manager',
        );
        //dd(__(key: 'laravel-app-version-manager::version-messages.deprecated_app_version.message', replace: ['appName' => config(key: 'laravel-app-version-manager.app_name')]));
    }

    protected function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function (): void {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }

    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config(key: 'laravel-app-version-manager.route.prefix', default: 'api/v1'),
            'middleware' => DeprecatedVersionCheckMiddleware::class,
        ];
    }

    protected function publishTranslations(): void
    {
        $this->publishes(
            paths: [__DIR__ . '/../resources/lang' => resource_path(path: 'lang/vendor/laravel-app-version-manager')],
            groups: 'translations',
        );
    }

    private function publishMigrations(): void
    {
        $migrations = [];

        if (empty(glob(database_path(path: 'migrations/*_create_app_versions_table.php')))) {
            $migrations[__DIR__ . '/../database/migrations/create_app_versions_table.php.stub'] = database_path(path: 'migrations/' . now()->format('Y_m_d_His') . '_create_app_versions_table.php');
        }

        if (empty(glob(database_path(path: 'migrations/*_create_app_version_history_table.php')))) {
            $migrations[__DIR__ . '/../database/migrations/create_app_version_history_table.php.stub'] = database_path(path: 'migrations/' . now()->format('Y_m_d_His') . '_create_app_version_history_table.php');
        }

        $this->publishes(
            paths: $migrations,
            groups: 'migrations',
        );
    }

    private function publishConfigs(): void
    {
        $configs = [__DIR__ . '/../config/laravel-app-version-manager.php' => config_path('laravel-app-version-manager.php')];

        $this->publishes(
            paths: $configs,
            groups: 'config',
        );
    }
}
