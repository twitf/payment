<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/22
 * Time: 13:15
 */

namespace twitf\Payment\Wechat;

class MpPay extends Pay
{
    const TRADE_TYPE = 'JSAPI';

    public function pay()
    {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $key = $this->config->remove('apiSecret');
        $this->config['sign'] = Help::MakeSign($this->config, $key);
        $xml = Help::arrayToXml($this->config);

        $response = $this->postXmlCurl($xml, $url, false);
        if (!$response) {
            return false;
        }
        $result = $this->xmlToArray($response);

        if ($result['result_code'] === 'SUCCESS' && $result['return_code'] === 'SUCCESS') {
            return $result;
        } else {
            //$result['err_msg'] = $this->error_code($result['err_code']);
            throw new BadRequestHttpException($result['err_code_des']);
        }
    }
}
