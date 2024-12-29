<?php

namespace HerepaySDK;

use Illuminate\Support\ServiceProvider;

class HerepayServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/herepay.php' => config_path('herepay.php'),
        ], 'config');
    }

    public function register()
    {
        $this->app->singleton('herepay', function ($app) {
            $config = config('herepay');
            return new HerepayService($config);
        });
    }
}
