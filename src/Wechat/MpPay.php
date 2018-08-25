<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/22
 * Time: 13:15
 */

namespace twitf\Payment\Wechat;

use twitf\Payment\Config;
use GuzzleHttp\Client;

class MpPay
{
    /**
     * 必传参数
     * @var array
     */
    const required = ['key', 'appid', 'mch_id', 'body', 'out_trade_no', 'total_fee', 'notify_url'];

    const TRADE_TYPE = 'JSAPI';

    public $config = [];

    public function __construct(Config $config)
    {
        $this->config = $config;
        Application::validateParams(self::required, $this->config);
    }

    public function pay()
    {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $params = [
            'appid' => $this->config->get('appid'),
            'mch_id' => $this->config->get('mch_id'),
            'body' => $this->config->get('body'),
            'out_trade_no' => $this->config->get('out_trade_no'),
            'total_fee' => $this->config->get('total_fee'),
            'notify_url' => $this->config->get('notify_url'),
            'nonce_str' => Help::getNonceStr(),
            'spbill_create_ip' => Help::getClientIp(),
            'trade_type' => self::TRADE_TYPE,
            'openid' => 'oOh8wxDs7Dk-ob0hAmYPneNEqnMI'
        ];
        $params['sign'] = Help::MakeSign($params, $this->config->get('key'));
        $xml = Help::arrayToXml($params);
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, false);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        curl_close($ch);
        var_dump(Help::xmlToArray($data));
        die;
        if ($result['result_code'] === 'SUCCESS' && $result['return_code'] === 'SUCCESS') {
            return $result;
        } else {
            //$result['err_msg'] = $this->error_code($result['err_code']);
            throw new \Exception($result['err_code_des']);
        }
    }
}
