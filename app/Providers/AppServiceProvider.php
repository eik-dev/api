<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

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
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // Configure Passport
        Passport::enablePasswordGrant();
        
        // Set token expiration times
        Passport::tokensExpireIn(now()->addMinutes(config('passport.token_expiration.access_token', 60)));
        Passport::refreshTokensExpireIn(now()->addMinutes(config('passport.token_expiration.refresh_token', 20160)));
        Passport::personalAccessTokensExpireIn(now()->addMinutes(config('passport.token_expiration.personal_access_token', 60)));

        // Define scopes
        Passport::tokensCan(config('passport.scopes', []));
        Passport::setDefaultScope(config('passport.default_scope', 'read-user'));
    }
}
