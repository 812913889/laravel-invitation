<?php

namespace Ariby\LaravelInvitation;

use Illuminate\Support\ServiceProvider;
use Ariby\LaravelInvitation\Commands\RoutineClearExpiredLaravelInvitation;

class LaravelInvitationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // 合併套件設定檔
            $this->mergeConfigFrom(
                __DIR__ . '/../config/laravel_invitation.php', 'laravel_invitation'
            );
        }

        // 發佈設定檔
        $this->publishes([
            __DIR__ . '/../config/laravel_invitation.php' => config_path('laravel_invitation.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations')
        ], 'migrations');

        // 執行所有套件 migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // 註冊所有 commands
        $this->commands([
            RoutineClearExpiredLaravelInvitation::class
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 合併套件設定檔
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel_invitation.php', 'laravel_invitation'
        );

        // 註冊所有 commands
        $this->commands([
            RoutineClearExpiredLaravelInvitation::class
        ]);

        $this->app->bind('laravel_invitation', LaravelInvitation::class);
        $this->app->singleton(LaravelInvitation::class, LaravelInvitation::class);
    }

}
