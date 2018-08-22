<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/21
 * Time: 17:50
 */

namespace Payment\Wechat;

use Payment\Config;

/**
 * 刷卡支付 目前没有接触过 待续
 * @method \Payment\Wechat\AppPay app() App支付
 * @method \Payment\Wechat\MiniPay mini() 小程序支付
 * @method \Payment\Wechat\MpPay mp() 公众号支付
 * @method \Payment\Wechat\ScanPay scan() 扫码支付
 * @method \Payment\Wechat\H5Pay wap() h5支付
 * @method \Payment\Wechat\MicroPay micro() h5支付
 */
class Application
{
    public $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        var_dump($this->config);
    }

    /**
     * @param $name
     * @param $arguments
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        $this->make($name);
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function make($name)
    {
        $value = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
        $application = __NAMESPACE__ . '\\' . $value . 'Pay';
        if (!class_exists($application)) {
            throw new \Exception("Class {$application} does not exist");
        }
        return new $application($this->config);
    }
}
