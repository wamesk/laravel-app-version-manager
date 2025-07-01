<?php

declare(strict_types=1);

namespace Wame\LaravelAppVersionManager;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Wame\LaravelAppVersionManager\Events\AppVersionUpdatedEvent;
use Wame\LaravelAppVersionManager\Http\Middleware\DeprecatedVersionCheckMiddleware;
use Wame\LaravelAppVersionManager\Jobs\SaveAppVersionHistoryJob;
use Wame\LaravelAppVersionManager\Models\AppVersion;
use Wame\LaravelAppVersionManager\Observers\AppVersionObserver;

class LaravelAppVersionManagerProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            // Export the migration
            $this->publishMigrations();

            // Export configs
            $this->publishConfigs();

            // Export views
            // $this->publishViews();

            // Register Commands
            // $this->commands([
            //    InstallLaravelAuth::class,
            // ]);
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
            path: __DIR__.'/../resources/lang',
            namespace: 'laravel-app-version-manager',
        );
    }

    protected function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function (): void {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }

    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config(key: 'laravel-app-version-manager.route.prefix', default: 'api/v1'),
            'middleware' => DeprecatedVersionCheckMiddleware::class,
        ];
    }

    private function publishMigrations(): void
    {
        $migrations = collect([
            'create_app_versions_table',
            'create_app_version_history_table',
            'add_platform_in_app_versions_table',
        ])
            ->filter(fn (string $migration) => empty(glob(database_path(path: "migrations/*_{$migration}.php"))))
            ->mapWithKeys(function (string $migration, int $index) {
                $dateFormat = now()->format('Y_m_d_His');

                return [
                    __DIR__."/../database/migrations/{$migration}.php.stub" => database_path("migrations/{$dateFormat}{$index}_{$migration}.php"),
                ];
            })
            ->toArray();

        $this->publishes(
            paths: $migrations,
            groups: 'migrations',
        );
    }

    private function publishConfigs(): void
    {
        $configs = [__DIR__.'/../config/laravel-app-version-manager.php' => config_path('laravel-app-version-manager.php')];

        $this->publishes(
            paths: $configs,
            groups: 'config',
        );
    }
}
