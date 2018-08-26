<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/22
 * Time: 13:17
 */

namespace twitf\Payment\Wechat;

use twitf\Payment\Config;

/**
 * 小程序支付
 * Class MiniPay
 * @package twitf\Payment\Wechat
 */
class MiniPay
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
}
