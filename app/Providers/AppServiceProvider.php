<?php

namespace App\Providers;

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
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::whereIn('key', ['smtp_host', 'smtp_port', 'smtp_email', 'smtp_password'])->pluck('value', 'key');
                if ($settings->get('smtp_host') && $settings->get('smtp_email') && $settings->get('smtp_password')) {
                    config([
                        'mail.mailers.smtp.host' => $settings->get('smtp_host'),
                        'mail.mailers.smtp.port' => $settings->get('smtp_port'),
                        'mail.mailers.smtp.username' => $settings->get('smtp_email'),
                        'mail.mailers.smtp.password' => $settings->get('smtp_password'),
                        'mail.mailers.smtp.encryption' => $settings->get('smtp_port') == 465 ? 'ssl' : 'tls',
                        'mail.from.address' => $settings->get('smtp_email'),
                        'mail.from.name' => 'REODA',
                    ]);
                }
            }
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
