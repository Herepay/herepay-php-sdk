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
        $this->app = new Container();

        $this->app->instance('config', [
            'herepay' => [
                'sandbox' => true,
                'secret_key' => 'test_secret_key',
                'api_key' => 'test_api_key',
                'private_key' => 'test_private_key',
            ],
        ]);

        $provider = new HerepayServiceProvider($this->app);
        $provider->register();
    }

    public function testHerepayServiceIsSingleton()
    {
        $herepay1 = $this->app->make('herepay');
        $herepay2 = $this->app->make('herepay');

        $this->assertSame($herepay1, $herepay2);
    }

    public function testHerepayServiceInstance()
    {
        $herepay = $this->app->make('herepay');

        $this->assertInstanceOf(HerepayService::class, $herepay);
    }

    public function testHerepayServiceReceivesConfig()
    {
        $herepay = $this->app->make('herepay');

        $config = $herepay->getConfig();
        $expectedConfig = [
            'sandbox' => $config['sandbox'],
            'secret_key' => $config['secret_key'],
            'api_key' => $config['api_key'],
            'private_key' => $config['private_key'],
        ];

        $this->assertEquals($expectedConfig, $herepay->getConfig());
    }
}
