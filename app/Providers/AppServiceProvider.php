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
            $linkedCompanies = collect();
            if ($user && $user->isExporter()) {
                $linkedCompanies = $user->companies()->orderBy('name')->get();
            }
            $view->with('notificationCount', $unread);
            $view->with('linkedCompanies', $linkedCompanies);
        });
    }
}
