<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/30
 * Time: 10:32
 */

namespace twitf\Payment\Alipay;

use twitf\Payment\Config;

class PcPay
{
    const PRODUCT_CODE = 'FAST_INSTANT_TRADE_PAY';

    const METHOD = 'alipay.trade.page.pay';

    public $config = [];

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param $params
     * @return 提交表单HTML文本
     */
    public function pay($url, $commonParams, $params)
    {
        $commonParams['method'] = self::METHOD;
        $params['product_code'] = self::PRODUCT_CODE;
        $commonParams['biz_content'] = json_encode($params);
        $commonParams['sign'] = Help::makeSign(Help::getSignContent($commonParams, $this->config->get('charset')), $this->config->get('application_private_key'), $this->config->get('sign_type'));
        return Help::buildRequestForm($url, $commonParams, $this->config->get('charset'));
    }
}
