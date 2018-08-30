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

    public $appRequired         = ['app_id', 'method', 'charset', 'sign_type', 'timestamp', 'version', 'notify_url', 'biz_content'];
    public $wapRequired         = ['appid', 'mch_id', 'key', 'body', 'out_trade_no', 'total_fee', 'notify_url', 'scene_info'];
    public $pcRequired          = ['appid', 'mch_id', 'key', 'body', 'out_trade_no', 'total_fee', 'notify_url', 'auth_code'];
    public $refundRequired      = ['appid', 'mch_id', 'key', 'out_trade_no|transaction_id', 'total_fee', 'out_refund_no', 'refund_fee', 'cert', 'ssl_key'];
    public $transferRequired    = [];

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
        $params['nonce_str'] = Help::getNonceStr();
        $params['spbill_create_ip'] = Help::getClientIp();
        return call_user_func_array([new $application($this->config), 'pay'], [$params]);
    }

    /**
     * 初始化配置 返回请求参数
     * @param $required
     * @param Config $config
     * @throws \Exception
     */
    public function initConfig($name, $arguments)
    {
        $validateName = $name . 'Required';
        $arguments = array_merge($this->config->get(), $arguments);
        foreach ($this->$validateName as $value) {
            if (strpos($value, '|')) {
                $value = explode('|', $value);
                if (!ArrayHelp::exists($arguments, $value[0]) && !ArrayHelp::exists($arguments, $value[1])) {
                    throw new \Exception(sprintf("Config attribute '%s' and '%s'  has at least one bottleneck.", $value[0], $value[1]));
                }
            } else {
                if (!ArrayHelp::exists($arguments, $value)) {
                    throw new \Exception(sprintf("Config attribute '%s' does not exist.", $value));
                }
            }
        }
        unset($arguments['key']);
        return $arguments;
    }
}
