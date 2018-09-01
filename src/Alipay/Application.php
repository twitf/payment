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

    public $appRequired      = ['app_id', 'merchant_private_key', 'alipay_public_key', 'notify_url', 'charset', 'sign_type', 'subject', 'out_trade_no', 'total_amount'];
    public $wapRequired      = ['app_id', 'merchant_private_key', 'alipay_public_key', 'return_url', 'notify_url', 'charset', 'sign_type', 'subject', 'out_trade_no', 'total_amount'];
    public $pcRequired       = ['app_id', 'merchant_private_key', 'alipay_public_key', 'return_url', 'notify_url', 'charset', 'sign_type', 'subject', 'out_trade_no', 'total_amount'];
    public $refundRequired   = ['app_id', 'merchant_private_key', 'alipay_public_key', 'out_trade_no|trade_no', 'charset', 'sign_type', 'refund_amount'];
    public $transferRequired = ['app_id', 'merchant_private_key', 'alipay_public_key', 'charset', 'sign_type', 'out_biz_no', 'payee_type', 'payee_account', 'amount'];
    public $queryRequired    = ['app_id', 'merchant_private_key', 'alipay_public_key', 'charset', 'sign_type', 'out_trade_no|trade_no'];
    public $closeRequired    = ['app_id', 'merchant_private_key', 'alipay_public_key', 'charset', 'sign_type', 'out_trade_no|trade_no'];
    public $cancelRequired   = ['app_id', 'merchant_private_key', 'alipay_public_key', 'charset', 'sign_type', 'out_trade_no|trade_no'];

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
     * 查询订单
     * @param $arguments
     * @param $isReturn 是否是退款订单 默认false
     */
    public function query($arguments, $isReturn = false)
    {
        //业务参数
        $params = $this->initConfig('query', $arguments);
        if ($isReturn && $this->config->exists('out_request_no')) {
            throw new \Exception("Config attribute 'out_request_no' does not exist.");
        }
        //公共参数
        $commonParams = $this->generateCommonParams($this->config->get());
        $commonParams['method'] = $isReturn ? 'alipay.trade.fastpay.refund.query' : 'alipay.trade.query';
        $commonParams['biz_content'] = json_encode($params);
        $commonParams['sign'] = Help::makeSign(Help::getSignContent($commonParams, $this->config->get('charset')), $this->config->get('merchant_private_key'), $this->config->get('sign_type'));
        return Help::requestApi($this->requestUrl, $commonParams, $this->config->get('alipay_public_key'));
    }

    /**
     * 用于交易创建后，用户在一定时间内未进行支付，可调用该接口直接将未付款的交易进行关闭。
     * @param $arguments
     */
    public function close($arguments)
    {
        //业务参数
        $params = $this->initConfig('close', $arguments);
        //公共参数
        $commonParams = $this->generateCommonParams($this->config->get());
        $commonParams['method'] = 'alipay.trade.close';
        $commonParams['biz_content'] = json_encode($params);
        $commonParams['sign'] = Help::makeSign(Help::getSignContent($commonParams, $this->config->get('charset')), $this->config->get('merchant_private_key'), $this->config->get('sign_type'));
        return Help::requestApi($this->requestUrl, $commonParams, $this->config->get('alipay_public_key'));
    }

    /**
     * 撤销订单
     * 只有发生支付系统超时或者支付结果未知时可调用撤销，其他正常支付的单如需实现相同功能请调用申请退款API
     * @return mixed
     * @throws \Exception
     */
    public function cancel()
    {
        //业务参数
        $params = $this->initConfig('cancel', $arguments);
        //公共参数
        $commonParams = $this->generateCommonParams($this->config->get());
        $commonParams['method'] = 'alipay.trade.cancel';
        $commonParams['biz_content'] = json_encode($params);
        $commonParams['sign'] = Help::makeSign(Help::getSignContent($commonParams, $this->config->get('charset')), $this->config->get('merchant_private_key'), $this->config->get('sign_type'));
        return Help::requestApi($this->requestUrl, $commonParams, $this->config->get('alipay_public_key'));
    }

    /**
     * 统一收单交易退款
     * @param $arguments 业务参数
     * @return mixed
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
        return Help::requestApi($this->requestUrl, $commonParams, $this->config->get('alipay_public_key'));
    }

    /**
     * 单笔转账到支付宝账户
     * @param $arguments 业务参数
     * @return mixed
     * @throws \Exception
     */
    public function transfer($arguments)
    {
        //业务参数
        $params = $this->initConfig('transfer', $arguments);
        //公共参数
        $commonParams = $this->generateCommonParams($this->config->get());
        $commonParams['method'] = 'alipay.fund.trans.toaccount.transfer';
        $commonParams['biz_content'] = json_encode($params);
        $commonParams['sign'] = Help::makeSign(Help::getSignContent($commonParams, $this->config->get('charset')), $this->config->get('merchant_private_key'), $this->config->get('sign_type'));
        return Help::requestApi($this->requestUrl, $commonParams, $this->config->get('alipay_public_key'));
    }

    /**
     * 回调验证签名
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function verify($params)
    {
        if ($this->config->exists('alipay_public_key')) {
            throw new \Exception("Config attribute 'alipay_public_key' does not exist.");
        }
        $signContent = Help::getSignContent($params, $params['charset'], true);
        return Help::verifySign($signContent, $params['sign'], $this->config->get('alipay_public_key'), $params['sign_type']);
    }
}
