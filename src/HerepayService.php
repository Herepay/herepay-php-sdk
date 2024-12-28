<?php

namespace HerepaySDK;

use Exception;
use HerepaySDK\Helpers\HttpClient;

class HerepayService
{
    private $baseUrl;
    private $clientSecret;
    private $redirectUri;

    public function __construct($config)
    {
        $this->baseUrl = $config['sandbox'] ? 'https://uat.herepay.org' : 'https://herepay.org';
        $this->clientSecret = $config['client_secret'];
        $this->redirectUri = $config['redirect_uri'];
    }

    public function post($url, $data)
    {
        return HttpClient::request('POST', $this->baseUrl . $url, $this->buildHeaders(), $data);
    }

    public function get($url)
    {
        return HttpClient::request('GET', $this->baseUrl . $url, $this->buildHeaders());
    }

    private function buildHeaders()
    {
        return [
            'User-Secret-Key' => $this->clientSecret,
        ];
    }

    public function getPaymentChannels()
    {
        return $this->get('/api/v1/herepay/payment/channels');
    }

    public function createTransaction($data)
    {
        return $this->post('/api/v1/herepay/initiate', $data);
    }

    public function getTransactionDetails($referenceCode)
    {
        return $this->get('/api/v1/herepay/transactions/' . $referenceCode);
    }
}
