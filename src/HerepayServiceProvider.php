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

    public function testHerepayServiceReceivesConfig()
    {
        $herepay = $this->app->make('herepay');

        $expectedConfig = [
            'sandbox' => true,
            'secret_key' => 'test_secret_key',
            'api_key' => 'test_api_key',
            'private_key' => 'test_private_key',
        ];

        $this->assertEquals($expectedConfig, $herepay->getConfig());
    }
}
