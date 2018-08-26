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
        if ($this->config->exists('redirect_url')) {
            unset($params['redirect_url']);
        }
        $result = Request::requestApi('pay/unifiedorder', $params, $this->config->get('key'));
        if ($this->config->exists('redirect_url')) {
            $result['mweb_url'] = $result['mweb_url'] . '&redirect_url=' . urlencode($this->config->get('redirect_url'));
        }
        return $result;
    }
}
