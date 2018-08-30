<?php

/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/21
 * Time: 16:48
 */

namespace twitf\Payment\Alipay;

use twitf\Payment\Config;
use twitf\Payment\ArrayHelp;

/**
 * @method \twitf\Payment\AliPay\AppPay app(array $arguments) App支付
 * @method \twitf\Payment\AliPay\wapPay wap(array $arguments) 手机网站支付
 * @method \twitf\Payment\AliPay\PcPay pc(array $arguments) 电脑网站支付
 */
class Application
{
    public $config = [];
    const  BASE_URL='https://openapi.alipay.com/gateway.do';
    public $appRequired = ['app_id', 'merchant_private_key', 'alipay_public_key', 'notify_url', 'charset', 'sign_type', 'subject', 'out_trade_no', 'total_amount'];
    public $wapRequired = ['app_id', 'merchant_private_key', 'alipay_public_key','return_url', 'notify_url', 'charset', 'sign_type', 'subject', 'out_trade_no', 'total_amount'];
    public $pcRequired = ['app_id', 'merchant_private_key', 'alipay_public_key','return_url', 'notify_url', 'charset', 'sign_type', 'subject', 'out_trade_no', 'total_amount'];
    public $refundRequired = ['appid', 'mch_id', 'key', 'out_trade_no|transaction_id', 'total_fee', 'out_refund_no', 'refund_fee', 'cert', 'ssl_key'];
    public $transferRequired = [];

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
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method. 只取第一个参数
        return $this->make($name, $arguments[0]);
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function make($name, $arguments)
    {
        $_application = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
        $application = __NAMESPACE__ . '\\' . $_application . 'Pay';
        if (!class_exists($application)) {
            throw new \Exception(sprintf("Class '%s' does not exist.", $application));
        }
        $params = $this->initConfig($name, $arguments);
        return call_user_func_array([new $application($this->config), 'pay'], [$params]);
    }

    /**
     * 初始化配置 返回业务参数
     * @param $required
     * @param Config $config
     * @throws \Exception
     */
    public function initConfig($name, $arguments)
    {
        $validateName = $name . 'Required';
        //组合必传参数数组
        $validateArray = array_merge($this->config->get(), $arguments);
        foreach ($this->$validateName as $value) {
            if (strpos($value, '|') !== false) {//二选一 参数
                $value = explode('|', $value);
                if (!ArrayHelp::exists($validateArray, $value[0]) && !ArrayHelp::exists($validateArray, $value[1])) {
                    throw new \Exception(sprintf("Config attribute '%s' and '%s'  has at least one bottleneck.", $value[0], $value[1]));
                }
            } else {
                if (!ArrayHelp::exists($validateArray, $value)) {
                    throw new \Exception(sprintf("Config attribute '%s' does not exist.", $value));
                }
            }
        }
        return $arguments;
    }
}
