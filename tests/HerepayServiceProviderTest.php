<?php

namespace HerepaySDK\Tests;

use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use HerepaySDK\HerepayServiceProvider;
use HerepaySDK\HerepayService;

class HerepayServiceProviderTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        // Create a mock application container
        $this->app = new Container();

        // Bind config to the container
        $this->app->instance('config', [
            'herepay' => [
                'sandbox' => true,
                'secret_key' => 'test_secret_key',
                'api_key' => 'test_api_key',
                'private_key' => 'test_private_key',
            ],
        ]);

        // Register the service provider
        $provider = new HerepayServiceProvider($this->app);
        $provider->register();
    }

    public function testHerepayServiceIsSingleton()
    {
        // Resolve 'herepay' from the container
        $herepay1 = $this->app->make('herepay');
        $herepay2 = $this->app->make('herepay');

        // Assert that the two instances are the same (singleton)
        $this->assertSame($herepay1, $herepay2);
    }

    public function testHerepayServiceInstance()
    {
        // Resolve 'herepay' from the container
        $herepay = $this->app->make('herepay');

        // Assert that it is an instance of HerepayService
        $this->assertInstanceOf(HerepayService::class, $herepay);
    }

    public function testHerepayServiceReceivesConfig()
    {
        // Resolve 'herepay' from the container
        $herepay = $this->app->make('herepay');

        // Assert that the config is passed correctly
        $expectedConfig = [
            'sandbox' => true,
            'secret_key' => 'test_secret_key',
            'api_key' => 'test_api_key',
            'private_key' => 'test_private_key',
        ];

        $this->assertEquals($expectedConfig, $herepay->getConfig());
    }
}
