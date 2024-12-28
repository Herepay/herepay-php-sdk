<?php

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        $config = [
            'herepay' => [
                'sandbox' => true,
                'secret_key' => 'test_secret_key',
                'api_key' => 'test_api_key',
                'private_key' => 'test_private_key',
            ],
        ];

        if ($key === null) {
            return $config;
        }

        return $config[$key] ?? $default;
    }
}
