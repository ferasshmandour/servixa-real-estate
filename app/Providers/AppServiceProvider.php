<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
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
