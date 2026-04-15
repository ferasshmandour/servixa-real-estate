<?php

namespace App\Providers;

use App\Auth\TabAwareSessionGuard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register the tab-aware session guard driver used by the admin guard.
        // It scopes each tab's session key with the _tab parameter so multiple
        // different admin accounts can be open simultaneously in different tabs.
        Auth::extend('tab-session', function ($app, $name, array $config) {
            $guard = new TabAwareSessionGuard(
                $name,
                Auth::createUserProvider($config['provider']),
                $app['session.store'],
                $app['request'],
            );
            $guard->setCookieJar($app['cookie']);
            $guard->setDispatcher($app['events']);
            $guard->setRequest($app->refresh('request', $guard, 'setRequest'));
            return $guard;
        });

        Model::preventLazyLoading(!app()->isProduction());

        Paginator::useTailwind();

        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        Passport::enablePasswordGrant();

        View::composer(['partials.sidebar', 'partials.header'], function ($view) {
            $admin = auth('admin')->user();
            $view->with([
                'adminUser'    => $admin,
                'isSuperAdmin' => $admin?->hasRole('super-admin') ?? false,
            ]);
        });
    }
}
