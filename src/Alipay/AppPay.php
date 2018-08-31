<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/30
 * Time: 10:31
 */

namespace twitf\Payment\Alipay;

use twitf\Payment\Config;

class AppPay
{

    const PRODUCT_CODE = 'QUICK_MSECURITY_PAY';

    const METHOD = 'alipay.trade.app.pay';

    public $config = [];

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function pay($url, $commonParams, $params)
    {
        $commonParams['method'] = self::METHOD;
        $params['product_code'] = self::PRODUCT_CODE;
        $commonParams['biz_content'] = json_encode($params);
        $commonParams['sign'] = Help::makeSign(Help::getSignContent($commonParams, $this->config->get('charset')), $this->config->get('merchant_private_key'), $this->config->get('sign_type'));
        return $commonParams;
    }
}
