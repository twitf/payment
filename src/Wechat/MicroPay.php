<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/22
 * Time: 13:36
 */

namespace twitf\Payment\Wechat;

use twitf\Payment\Config;

/**
 * 刷卡支付
 * Class MicroPay
 * @package twitf\Payment\Wechat
 */
class MicroPay
{


    const TRADE_TYPE = 'MICROPAY';

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
