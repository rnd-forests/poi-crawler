<?php

namespace App\Modules\Auth\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Passport::tokensExpireIn(now()->addDays(config('auth.oauth.lifetime.token')));

        Passport::refreshTokensExpireIn(now()->addDays(config('auth.oauth.lifetime.refresh_token')));

        Passport::enableImplicitGrant();

        // Passport::tokensCan([
        //     'locations-read' => 'Read locations, location types',
        // ]);
    }
}
