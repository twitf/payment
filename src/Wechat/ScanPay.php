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
 * 扫码支付
 * Class ScanPay
 * @package twitf\Payment\Wechat
 */
class ScanPay
{
    const REQUIRED = ['body', 'out_trade_no', 'total_fee', 'notify_url', 'product_id'];

    public $config = [];

    const TRADE_TYPE = 'NATIVE';

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
}
