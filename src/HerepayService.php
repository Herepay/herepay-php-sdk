<?php

namespace HerepaySDK;

use Exception;
use HerepaySDK\Helpers\HttpClient;

class HerepayService
{
    private $baseUrl;
    private $secretKey;
    private $apiKey;
    private $privateKey;

    /**
     * Constructor to initialize HerepayService with configuration.
     *
     * @param array $config Configuration array with keys:
     *                      - sandbox: bool (true for sandbox, false for production)
     *                      - secret_key: string
     *                      - api_key: string
     *                      - private_key: string
     * @throws Exception
     */
    public function __construct()
    {
        $config = require 'config/herepay.php';

        if (!isset($config['secret_key'], $config['api_key'], $config['private_key'])) {
            throw new Exception('Missing required configuration parameters.');
        }

        $this->baseUrl = $config['sandbox'] ? 'https://uat.herepay.org' : 'https://app.herepay.org';
        $this->secretKey = $config['secret_key'];
        $this->apiKey = $config['api_key'];
        $this->privateKey = $config['private_key'];
    }

    /**
     * Send a POST request.
     *
     * @param string $url API endpoint.
     * @param array $data Data to send in the request body.
     * @return array Response body.
     * @throws Exception
     */
    public function post(string $url, array $data)
    {
        return HttpClient::request('POST', $this->baseUrl . $url, $this->buildHeaders(), $data);
    }

    /**
     * Send a GET request.
     *
     * @param string $url API endpoint.
     * @return array Response body.
     * @throws Exception
     */
    public function get(string $url)
    {
        return HttpClient::request('GET', $this->baseUrl . $url, $this->buildHeaders());
    }

    /**
     * Build request headers.
     *
     * @return array
     */
    private function buildHeaders(): array
    {
        return [
            'SecretKey' => $this->secretKey,
            'XApiKey' => $this->apiKey,
        ];
    }

    /**
     * Get available payment channels.
     *
     * @return array
     * @throws Exception
     */
    public function getPaymentChannels(): array
    {
        $response = $this->get('/api/v1/herepay/payment/channels');

        return $response;
    }


    /**
     * Create a new transaction.
     *
     * @param array $data Transaction data.
     * @return array
     * @throws Exception
     */
    public function initiate(array $data): array
    {
        $requiredFields = ['amount', 'description', 'payment_code', 'created_at', 'name', 'email', 'phone', 'bank_prefix', 'payment_method'];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required transaction parameter: {$field}");
            }
        }

        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            throw new Exception('Invalid amount. Must be a positive numeric value.');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format.');
        }

        if (!is_string($data['description']) || empty($data['description'])) {
            throw new Exception('Invalid description. Must be a non-empty string.');
        }

        if (!preg_match('/^\d+$/', $data['phone'])) {
            throw new Exception('Invalid phone number. Must contain only digits.');
        }

        $this->post('/api/v1/herepay/initiate', $data);
    }


    /**
     * Get transaction details by reference code.
     *
     * @param string $referenceCode Transaction reference code.
     * @return array
     * @throws Exception
     */
    public function getTransactionDetails(string $referenceCode): array
    {
        if (empty($referenceCode)) {
            throw new Exception('Reference code cannot be empty.');
        }

        $response = $this->get('/api/v1/herepay/transactions/' . $referenceCode);
        return $response;
    }

    /**
     * Get latest transaction.
     *
     * @param string $referenceCode Transaction reference code.
     * @return array
     * @throws Exception
     */
    public function getTransactions()
    {
        $response = $this->get('/api/v1/herepay/transactions');
        return $response;
    }

    /**
     * Generate a checksum for data validation.
     *
     * @param array $data Data for which checksum is to be generated.
     * @return string
     * @throws Exception
     */
    public function generateChecksum(array $data): string
    {
        ksort($data);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = json_encode($value);
            }
        }

        $dataString = implode(',', $data);
        $privatekey = openssl_pkey_get_private($this->privateKey);

        return hash_hmac('sha256', $dataString, $privatekey);
    }

    public function getConfig(): array
    {
        $config = require 'config/herepay.php';
        return $config;
    }
}
