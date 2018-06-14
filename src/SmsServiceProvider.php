<?php

namespace Neurohotep\LaravelSms;

use Illuminate\Support\ServiceProvider;

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
        
        $this->app->singleton('sms', function($app) {
            $config = $app->make('config');
            $login = $config->get('login');
            $password = $config->get('password');
            $user_group = $config->get('user_group');
            return new MtsSms($login, $password, $user_group);
        });
    }

    public function provides() {
        return ['sms'];
    }
}
