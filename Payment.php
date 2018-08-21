<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/21
 * Time: 14:01
 */

namespace Payment;


/**
 * @method static \Payment\Alipay\Application alipay(string $name, array $config) 支付宝
 * @method static \Payment\Wechat\Application wechat(string $name, array $config) 微信
 */
class Payment
{
    /**
     * @param $name
     * @param array $config
     * @return mixed
     * @throws \Exception
     */
    public static function make($name, array $config)
    {
        $value = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
        $application = __NAMESPACE__ . '\\' . $value . '\\Application';
        if (!class_exists($application)) {
            throw new \Exception("Class {$application} does not exist");
        }
        return new $application($config);
    }

    /**
     * @param $name
     * @param $config
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($name, $config)
    {
        return self::make($name, ...$config);
    }
}

