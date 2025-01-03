<?php

namespace HerepaySDK;

class Herepay
{
    private static $instance;

    public static function init()
    {
        self::$instance = new HerepayService();
    }

    public static function __callStatic($method, $args)
    {
        if (!self::$instance) {
            throw new \Exception('Herepay SDK not initialized. Call Herepay::init() first.');
        }

        return call_user_func_array([self::$instance, $method], $args);
    }
}
