<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/22
 * Time: 13:22
 */

namespace twitf\Payment\Wechat;

use twitf\Payment\Config;

/**
 * APP支付
 * Class AppPay
 * @package twitf\Payment\Wechat
 */
class AppPay
{


    const TRADE_TYPE = 'APP';

    public $config = [];

    /**
     * MpPay constructor.
     * @param Config $config
     * @throws \Exception
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 统一下单
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function pay($params)
    {
        $params['trade_type'] = self::TRADE_TYPE;
        $result = Help::requestApi('pay/unifiedorder', $params, $this->config->get('key'));
        $data['appid']      = $result['appid'];    //appid
        $data['partnerid']  = $result['mch_id'];
        $data['prepayid']   = $result['prepay_id'];
        $data['package']    = 'Sign=WXPay';
        $data['noncestr']   = Help::getNonceStr();
        $data['timestamp']  = (string)time();
        $data['sign']       = Help::MakeSign($data,$this->config->get('key'));
        return $data;
    }
}
