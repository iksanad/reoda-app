<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        try {
            // SMTP settings override has been moved to Render Environment Variables
        } catch (\Exception $e) {
            // Abaikan error saat database belum siap (misal saat migrate)
        }

        \Illuminate\Support\Facades\View::composer('layouts.*', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                $recentNotifications = \App\Models\AppNotification::where('user_id', $user->id)
                    ->latest()
                    ->take(5)
                    ->get();
                $unreadNotificationsCount = \App\Models\AppNotification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count();
                
                $view->with('recentNotifications', $recentNotifications)
                     ->with('unreadNotificationsCount', $unreadNotificationsCount);
            }
        });
    }
}
