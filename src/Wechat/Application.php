<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/21
 * Time: 17:50
 */

namespace twitf\Payment\Wechat;

use twitf\Payment\Config;

/**
 * @method \twitf\Payment\Wechat\AppPay app() App支付
 * @method \twitf\Payment\Wechat\MiniPay mini() 小程序支付
 * @method \twitf\Payment\Wechat\MpPay mp() 公众号支付
 * @method \twitf\Payment\Wechat\ScanPay scan() 扫码支付
 * @method \twitf\Payment\Wechat\H5Pay wap() h5支付
 * @method \twitf\Payment\Wechat\MicroPay micro() 刷卡支付 _______目前没有接触过 待续
 */
class Application
{
    public $config = [];
    /**
     * 必传参数
     * @var array
     */
    const required = [
        'key',
        'appid',
        'mch_id',
        'body',
        'out_trade_no',
        'total_fee',
        'spbill_create_ip',
        'notify_url',
    ];

    /**
     * Application constructor.
     * @param Config $config
     * @throws \Exception
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
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
        foreach (self::required as $value) {
            if ($this->config->exists($value)) {
                throw new \Exception(sprintf("Config attribute '%s' does not exist.", $value));
            }
        }
        $value = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
        $application = __NAMESPACE__ . '\\' . $value . 'Pay';
        if (!class_exists($application)) {
            throw new \Exception(sprintf("Class '%s' does not exist.", $application));
        }
        return call_user_func_array([$application, $name], $this->config);
    }
}
