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

    //请求地址
    public $requestUrl = '';

    public $appRequired = ['app_id', 'merchant_private_key', 'alipay_public_key', 'notify_url', 'charset', 'sign_type', 'subject', 'out_trade_no', 'total_amount'];
    public $wapRequired = ['app_id', 'merchant_private_key', 'alipay_public_key', 'return_url', 'notify_url', 'charset', 'sign_type', 'subject', 'out_trade_no', 'total_amount'];
    public $pcRequired = ['app_id', 'merchant_private_key', 'alipay_public_key', 'return_url', 'notify_url', 'charset', 'sign_type', 'subject', 'out_trade_no', 'total_amount'];
    public $refundRequired = ['app_id', 'merchant_private_key', 'alipay_public_key', 'out_trade_no|trade_no', 'charset', 'sign_type', 'refund_amount'];
    public $transferRequired = [];

    /**
     * Application constructor.
     * @param Config $config
     * @throws \Exception
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->requestUrl = Help::setBaseUrl($this->config->exists('mod') && $this->config->get('mod') == 'sandbox');
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
        //业务参数
        $params = $this->initConfig($name, $arguments);
        //公共参数
        $commonParams = $this->generateCommonParams($this->config->get());
        return call_user_func_array([new $application($this->config), 'pay'], [$this->requestUrl, $commonParams, $params]);
    }

    /**
     * 验证必传参数 返回业务参数
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

    /**
     * 生成公共参数
     * @param $config
     * @return mixed
     */
    public function generateCommonParams($config)
    {
        $config['format'] = 'JSON';
        $config['charset'] = $this->config->get('charset');//'UTF-8';
        $config['sign_type'] = $this->config->get('sign_type');//'RSA2'
        $config['version'] = '1.0';
        $config['timestamp'] = date('Y-m-d H:i:s', time());
        //清理请求无用的参数
        unset($config['merchant_private_key']);
        unset($config['alipay_public_key']);
        unset($config['mod']);
        return $config;
    }

    /**
     * @param array $arguments
     * @throws \Exception
     */
    public function refund($arguments)
    {
        //业务参数
        $params = $this->initConfig('refund', $arguments);
        //公共参数
        $commonParams = $this->generateCommonParams($this->config->get());
        $commonParams['method'] = 'alipay.trade.refund';
        $commonParams['biz_content'] = json_encode($params);
        $commonParams['sign'] = Help::makeSign(Help::getSignContent($commonParams, $this->config->get('charset')), $this->config->get('merchant_private_key'), $this->config->get('sign_type'));
        Help::requestApi($this->requestUrl, $commonParams,$this->config->get('alipay_public_key'));
    }
}
