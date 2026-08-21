<?php

namespace App\Providers;

use App\Services\JejakService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['components.layouts.exporter', 'components.layouts.fama'], function ($view) {
            $user = auth()->user();
            $unread = 0;
            if ($user) {
                $unread = app(JejakService::class)->unreadNotificationCount($user->id);
            }
            $view->with('notificationCount', $unread);
        });
    }
}
