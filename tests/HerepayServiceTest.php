<?php

namespace HerepaySDK\Tests;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use HerepaySDK\HerepayService;
use PHPUnit\Framework\TestCase;


class HerepayServiceTest extends TestCase
{
    protected $herepay;

    protected function setUp(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $requiredKeys = ['HEREPAY_SANDBOX', 'HEREPAY_SECRET_KEY', 'HEREPAY_API_KEY', 'HEREPAY_PRIVATE_KEY'];
        foreach ($requiredKeys as $key) {
            if (empty($_ENV[$key])) {
                throw new \Exception("Environment variable {$key} is missing or empty.");
            }
        }

        $this->herepay = new HerepayService([
            'sandbox' => filter_var($_ENV['HEREPAY_SANDBOX'], FILTER_VALIDATE_BOOLEAN),
            'secret_key' => $_ENV['HEREPAY_SECRET_KEY'],
            'api_key' => $_ENV['HEREPAY_API_KEY'],
            'private_key' => $_ENV['HEREPAY_PRIVATE_KEY'],
        ]);
    }

    public function testGetPaymentChannels()
    {
        $response = $this->herepay->getPaymentChannels();

        $this->assertIsArray($response, 'Expected response to be an array.');

        $this->assertArrayHasKey('status', $response, 'Response does not contain key "status".');
        $this->assertArrayHasKey('data', $response, 'Response does not contain key "data".');
        $this->assertArrayHasKey('message', $response, 'Response does not contain key "message".');

        $this->assertEquals('success', $response['status'], 'Expected status to be "success".');

        $this->assertIsArray($response['data'], 'Expected "data" to be an array.');

        foreach ($response['data'] as $paymentMethod) {
            $this->assertArrayHasKey('payment_method', $paymentMethod, 'Payment method does not contain key "payment_method".');

            $this->assertArrayHasKey('charges', $paymentMethod, 'Payment method does not contain key "charges".');
            $this->assertIsArray($paymentMethod['charges'], 'Expected "charges" to be an array.');

            $this->assertArrayHasKey('channels', $paymentMethod, 'Payment method does not contain key "channels".');
            $this->assertIsArray($paymentMethod['channels'], 'Expected "channels" to be an array.');

            foreach ($paymentMethod['channels'] as $channel) {
                $this->assertArrayHasKey('prefix', $channel, 'Channel does not contain key "prefix".');
                $this->assertArrayHasKey('active', $channel, 'Channel does not contain key "active".');
                $this->assertArrayHasKey('name', $channel, 'Channel does not contain key "name".');
                $this->assertArrayHasKey('img_url', $channel, 'Channel does not contain key "img_url".');
            }
        }
    }

    public function testInitiate()
    {
        $transactionData = $this->exampleTransactionData();
        
        $transactionData['checksum'] = $this->generateTestChecksum($transactionData); 
        $response = $this->herepay->post('/api/v1/herepay/initiate', $transactionData);
        $this->assertNull($response, 'Expected response to be null because it redirecting.');
    }

    public function testGetTransactionDetails()
    {
        $referenceCode = $_ENV['HEREPAY_REFERENCE_CODE'];

        $response = $this->herepay->getTransactionDetails($referenceCode);

        $this->assertIsArray($response, 'Expected response to be an array.');
    }

    public function testGenerateChecksum()
    {
        $data = $this->exampleTransactionData();

        $checksum = $this->generateTestChecksum($data);

        $this->assertIsString($checksum, 'Expected checksum to be a string.');
        $this->assertNotEmpty($checksum, 'Checksum is empty.');
    }

    private function generateTestChecksum(array $data): string
    {
        return $this->herepay->generateChecksum($data);
    }

    private function exampleTransactionData(): array
    {
        return [
            'payment_code' => 'HP-SDK-' . strtoupper(bin2hex(random_bytes(5))),
            'created_at' => date('Y-m-d H:i:s'), 
            'amount' => rand(10, 1000),                  
            'name' => 'AliffRosli',                
            'email' => 'aliff.rosli96@gmail.com',    
            'phone' => '0123456789',            
            'description' => 'PAY SDK TEST', 
            'bank_prefix' => 'TEST0021',         
            'payment_method' => 'Online Banking',           
        ];
    }
}
