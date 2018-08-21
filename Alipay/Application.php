<?php

/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/21
 * Time: 16:48
 */

namespace Payment\Alipay;

class Application
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->gateway = Support::baseUri($this->config->get('mode', 'normal'));
        $this->payload = [
            'app_id' => $this->config->get('app_id'),
            'method' => '',
            'format' => 'JSON',
            'charset' => 'utf-8',
            'sign_type' => 'RSA2',
            'version' => '1.0',
            'return_url' => $this->config->get('return_url'),
            'notify_url' => $this->config->get('notify_url'),
            'timestamp' => date('Y-m-d H:i:s'),
            'sign' => '',
            'biz_content' => '',
        ];
    }
}
