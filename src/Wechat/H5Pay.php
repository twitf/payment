<?php

/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/22
 * Time: 13:06
 */

namespace twitf\Payment\Wechat;

use twitf\Payment\Config;

class H5Pay
{
    const REQUIRED = ['body', 'out_trade_no', 'total_fee', 'notify_url','scene_info'];

    const TRADE_TYPE = 'MWEB';

    public $config = [];

    /**
     * MpPay constructor.
     * @param Config $config
     * @throws \Exception
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        Application::validateConfig(self::REQUIRED, $this->config);
    }

    /**
     * 统一下单
     * @return mixed
     * @throws \Exception
     */
    public function pay()
    {
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
            'openid' => 'oOh8wxDs7Dk-ob0hAmYPneNEqnMI',
            'scene_info'=>$this->config
        ];
        $params['sign'] = Help::MakeSign($params, $this->config->get('key'));
        $xml = Help::arrayToXml($params);
        $result = Help::xmlToArray(Request::requestApi('pay/unifiedorder', $xml));
        if (!isset($result['return_code']) || $result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            throw new \Exception(sprintf("Wechat API Error '%s'.", $result['return_msg'] . (isset($result['err_code_des']) ?: '')));
        }
        $result['mweb_url'].'&redirect_url='.urlencode(Help::getCurrentUrl());
    }
}
