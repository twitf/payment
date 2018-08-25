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
    const REQUIRED = ['body', 'out_trade_no', 'total_fee', 'notify_url'];

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
}
