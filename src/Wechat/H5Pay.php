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
    const TRADE_TYPE = 'MWEB';

    public $config = [];
    
    public $redirect_url = '';
    
    /**
     * MpPay constructor.
     * @param Config $config
     * @throws \Exception
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 统一下单
     * @param $params
     * @return string
     * @throws \Exception
     */
    public function pay($params)
    {
        $params['trade_type'] = self::TRADE_TYPE;
        if (isset($params['redirect_url'])) {
            $this->redirect_url = $params['redirect_url'];
            unset($params['redirect_url']);
        }
        $result = Help::requestApi('pay/unifiedorder', $params, $this->config->get('key'));
        if (!empty($this->redirect_url)) {
            $result['mweb_url'] .= '&redirect_url=' . urlencode($this->redirect_url);
        }
        return $result;
    }
}
