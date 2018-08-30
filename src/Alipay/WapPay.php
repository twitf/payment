<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/30
 * Time: 10:31
 */

namespace twitf\Payment\Alipay;
use twitf\Payment\Config;
class WapPay
{
    const PRODUCT_CODE = 'QUICK_WAP_WAY';

    const METHOD = 'alipay.trade.wap.pay';

    public $config = [];

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function pay($params)
    {
        $commonParams = $this->config->get();
        $commonParams['format'] = 'JSON';
        $commonParams['charset'] = $this->config->get('charset');//'UTF-8';
        $commonParams['sign_type'] = $this->config->get('sign_type');//'RSA2'
        $commonParams['version'] = '1.0';
        $commonParams['timestamp'] = date('Y-m-d H:i:s', time());
        $commonParams['method'] = self::METHOD;
        $params['product_code'] = self::PRODUCT_CODE;
        $commonParams['biz_content'] = json_encode($params);
        unset($commonParams['merchant_private_key']);
        unset($commonParams['alipay_public_key']);
        $commonParams['sign'] = Help::makeSign(Help::getSignContent($commonParams, $this->config->get('charset')), $this->config->get('merchant_private_key'), $this->config->get('sign_type'));
        return Help::buildRequestForm(Application::BASE_URL,$commonParams,$this->config->get('charset'));
    }
}
