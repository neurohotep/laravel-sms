<?php

namespace Neirototam\MtsCommunicator;

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
            __DIR__.'/../config/mts.php' => config_path('mtscommunicator.php'),
        ], 'mtscommunicator');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->mergeConfigFrom( __DIR__.'/../config/mts.php', 'mtscommunicator');
        
        $this->app->singleton('mtscommunicator', function($app) {
            $config = $app->make('config');
            $login = $config->get('login');
            $password = $config->get('password');
            return new MtsSms($login, $password);
        });
    }

    public function provides() {
        return ['mtscommunicator'];
    }
}
