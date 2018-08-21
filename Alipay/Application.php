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
    protected $params;

    public function __construct($config)
    {
        $this->config = $config;
        $this->params = [
            'app_id' => $config['app_id'],
            'method' => '',
            'format' => 'JSON',
            'charset' => 'utf-8',
            'sign_type' => 'RSA2',
            'version' => '1.0',
            'return_url' => $config['return_url'],
            'notify_url' => $config['notify_url'],
            'timestamp' => date('Y-m-d H:i:s'),
            'sign' => '',
            'biz_content' => '',
        ];
    }
}
