<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/22
 * Time: 13:15
 */

namespace twitf\Payment\Wechat;

use twitf\Payment\Config;

/**
 * 公众号支付
 * Class MpPay
 * @package twitf\Payment\Wechat
 */
class MpPay
{


    const TRADE_TYPE = 'JSAPI';

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
     * @return string
     * @throws \Exception
     */
    public function pay($params)
    {
        $params['trade_type'] = self::TRADE_TYPE;
        $result = Help::requestApi('pay/unifiedorder', $params, $this->config->get('key'));
        return Help::getJsApiParameters($result, $this->config->get('key'));
    }
}
