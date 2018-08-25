<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/21
 * Time: 17:50
 */

namespace twitf\Payment\Wechat;

use twitf\Payment\ArrayHelp;
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

    public $params = [];
    const COMMON_REQUIRED = ['appid', 'mch_id', 'key'];
    const APP_REQUIRED = ['body', 'out_trade_no', 'total_fee', 'notify_url'];
    const MINI_REQUIRED = ['body', 'out_trade_no', 'total_fee', 'notify_url'];
    const MP_REQUIRED = ['body', 'out_trade_no', 'total_fee', 'notify_url'];
    const H5_REQUIRED = ['body', 'out_trade_no', 'total_fee', 'notify_url'];
    const SCAN_REQUIRED = ['body', 'out_trade_no', 'total_fee', 'notify_url', 'product_id'];
    const MICRO_REQUIRED = ['body', 'out_trade_no', 'total_fee', 'notify_url', 'auth_code'];

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
        $this->validateParams($name,$this->config);
        $name = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
        $application = __NAMESPACE__ . '\\' . $name . 'Pay';
        if (!class_exists($application)) {
            throw new \Exception(sprintf("Class '%s' does not exist.", $application));
        }
        return call_user_func_array([new $application($this->config), 'pay'], []);
    }

    /**
     * @param $required
     * @param Config $config
     * @throws \Exception
     */
    public function validateParams($name, Config $config)
    {
        $required=str_replace(' ', '', strtoupper(str_replace(['-', '_'], ' ', $name))) . '_REQUIRED';
        var_dump(self::$required);die;
        $required = self::$required;
        var_dump($required);die;
        foreach ($required as $value) {
            if (!$config->exists($value)) {
                throw new \Exception(sprintf("Config attribute '%s' does not exist.", $value));
            }
        }
    }
}
