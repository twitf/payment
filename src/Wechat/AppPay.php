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
}
