<?php

namespace Neurohotep\LaravelSms;

use Illuminate\Support\ServiceProvider;
use Neurohotep\LaravelSms\Drivers\SmsContract;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/sms.php' => config_path('sms.php'),
        ], 'sms');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->mergeConfigFrom( __DIR__.'/../config/sms.php', 'sms');

        $this->app->singleton(SmsManager::class, function ($app) {
            return new SmsManager($app);
        });

        $this->app->singleton('sms', function ($app) {
            return $app->make(SmsManager::class)->connection();
        });
    }

    public function provides() {
        return ['sms'];
    }
}
