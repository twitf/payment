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
 * @method \twitf\Payment\Wechat\AppPay app(array $arguments) App支付
 * @method \twitf\Payment\Wechat\MiniPay mini(array $arguments) 小程序支付
 * @method \twitf\Payment\Wechat\MpPay mp(array $arguments) 公众号支付
 * @method \twitf\Payment\Wechat\ScanPay scan(array $arguments) 扫码支付
 * @method \twitf\Payment\Wechat\H5Pay h5(array $arguments) h5支付
 * @method \twitf\Payment\Wechat\MicroPay micro(array $arguments) 刷卡支付 _______目前没有接触过 待续
 */
class Application
{
    public $config = [];

    public $appRequired     = ['appid', 'mch_id', 'key', 'body', 'out_trade_no', 'total_fee', 'notify_url'];
    public $h5Required      = ['appid', 'mch_id', 'key', 'body', 'out_trade_no', 'total_fee', 'notify_url', 'scene_info'];
    public $microRequired   = ['appid', 'mch_id', 'key', 'body', 'out_trade_no', 'total_fee', 'notify_url', 'auth_code'];
    public $miniRequired    = ['appid', 'mch_id', 'key', 'body', 'out_trade_no', 'total_fee', 'notify_url'];
    public $mpRequired      = ['appid', 'mch_id', 'key', 'body', 'out_trade_no', 'total_fee', 'notify_url'];
    public $scanRequired    = ['appid', 'mch_id', 'key', 'body', 'out_trade_no', 'total_fee', 'notify_url', 'product_id'];
    public $refundRequired  = ['appid', 'mch_id', 'key', 'out_trade_no|transaction_id', 'total_fee', 'out_refund_no', 'refund_fee', 'cert', 'ssl_key'];

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
     * @param $arguments
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
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public function initConfig($name, $arguments)
    {
        $validateName = $name . 'Required';
        $arguments = array_merge($this->config->get(), $arguments);
        foreach ($this->$validateName as $value) {
            if (strpos($value, '|')!==false) {
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

    /**
     * 退款
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public function refund($arguments)
    {
        $params = $this->initConfig('refund', $arguments);
        $params['nonce_str'] = Help::getNonceStr();
        unset($params['cert']);
        unset($params['ssl_key']);
        $result = Request::requestApi('secapi/pay/refund', $params, $this->config->get('key'), [
            'cert' => $arguments['cert'],
            'ssl_key' => $arguments['ssl_key']
        ]);
        return $result;
    }

    /**
     * 验证签名
     * @param $key
     * @return bool|mixed
     * @throws \Exception
     */
    public function verify($key)
    {
        $xml = file_get_contents("php://input");
        $notify = Help::xmlToArray($xml);
        if (Help::makeSign($notify, $key) === $notify['sign']) {
            if ($notify['return_code'] != 'SUCCESS' || $notify['result_code'] != 'SUCCESS') {
                throw new \Exception(sprintf("Wechat Notify Error '%s'.", $notify['return_msg'] . (isset($notify['err_code_des']) ? ':' . $notify['err_code_des'] : '')));
            }
            return $notify;
        }
        return false;
    }
}
