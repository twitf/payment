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
 * @method \twitf\Payment\Wechat\H5Pay h5() h5支付
 * @method \twitf\Payment\Wechat\MicroPay micro() 刷卡支付 _______目前没有接触过 待续
 */
class Application
{
    public $config = [];

    public $params = [];

    public $appRequired     = ['appid', 'mch_id', 'key', 'body', 'out_trade_no', 'total_fee', 'notify_url'];
    public $h5Required      = ['appid', 'mch_id', 'key', 'body', 'out_trade_no', 'total_fee', 'notify_url', 'scene_info','redirect_url'];
    public $microRequired   = ['appid', 'mch_id', 'key', 'body', 'out_trade_no', 'total_fee', 'notify_url', 'auth_code'];
    public $miniRequired    = ['appid', 'mch_id', 'key', 'body', 'out_trade_no', 'total_fee', 'notify_url'];
    public $mpRequired      = ['appid', 'mch_id', 'key', 'body', 'out_trade_no', 'total_fee', 'notify_url'];
    public $scanRequired    = ['appid', 'mch_id', 'key', 'body', 'out_trade_no', 'total_fee', 'notify_url', 'product_id'];

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
        // TODO: Implement __call() method.
        return $this->make($name);
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function make($name)
    {
        $_application = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
        $application = __NAMESPACE__ . '\\' . $_application . 'Pay';
        if (!class_exists($application)) {
            throw new \Exception(sprintf("Class '%s' does not exist.", $application));
        }
        $validateName = $name . 'Required';
        self::validateConfig($this->$validateName, $this->config);
        $this->params = $this->config->get();
        $this->params['nonce_str'] = Help::getNonceStr();
        $this->params['spbill_create_ip'] = Help::getClientIp();
        unset($this->params['key']);
        return call_user_func_array([new $application($this->config), 'pay'], [$this->params]);
    }

    /**
     * @param $required
     * @param Config $config
     * @throws \Exception
     */
    public static function validateConfig($required, Config $config)
    {
        foreach ($required as $value) {
            if (!$config->exists($value)) {
                throw new \Exception(sprintf("Config attribute '%s' does not exist.", $value));
            }
        }
    }
}
