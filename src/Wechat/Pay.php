<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/22
 * Time: 17:49
 */

namespace twitf\Payment\Wechat;

use twitf\Payment\Config;

abstract class Pay
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->config['nonce_str']=Help::getNonceStr();
        $this->config['sign']=Help::MakeSign($this->config,);
    }

    public function pay(){}

}
