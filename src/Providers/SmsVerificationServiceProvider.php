<?php

namespace Lluminate\SmsVerification\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Lluminate\SmsVerification\Clients\TwilioClient;
use Illuminate\Foundation\Console\AboutCommand;

class SmsVerificationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // 1. Publish config
        $this->publishes([
            __DIR__.'/../../config/sms-verification.php' => config_path('sms-verification.php'),
        ], 'sms-verification-config');

        // 2. Merge config (so we can access config values from our package)
        $this->mergeConfigFrom(
            __DIR__.'/../../config/sms-verification.php', 
            'sms-verification'
        );

        // 3. Publish migrations
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
        ], 'sms-verification-migrations');

        // 4. Load views & publish them
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'sms-verification');
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/sms-verification'),
        ], 'sms-verification-views');

        // 5. Load routes
        $this->loadRoutes();

        AboutCommand::macro('sms-verification', function (AboutCommand $command) {
            $command->comment('  Sms Verification:');
            $command->comment('    - Version: v1.0.0');
            $command->comment('    - Description: A package for verifying phone numbers via SMS.');
        });
    }

    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(TwilioClient::class, function ($app) {
            return new TwilioClient(
                config('sms-verification.twilio.account_sid'),
                config('sms-verification.twilio.auth_token'),
                config('sms-verification.twilio.verify_service_sid'),
            );
        });
    }

    protected function loadRoutes()
    {
        Route::group([
            'namespace' => 'Lluminate\\SmsVerification\\Http\\Controllers',
            'middleware' => ['web'], // or any other middlewares
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        });
    }
}