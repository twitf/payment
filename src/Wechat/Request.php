<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/26
 * Time: 11:30
 */

namespace twitf\Payment\Wechat;

class Request
{
    use \twitf\Payment\HttpRequest;

    private static $instance;

    protected $baseUri = 'https://api.mch.weixin.qq.com/';

    //表示等待服务器响应超时的最大值，使用 0 将无限等待 (默认行为).
    protected $connect_timeout = 5;

    //请求超时的秒数。使用 0 无限期的等待(默认行为)。
    protected $timeout = 5;

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;

    }

    /**
     * 请求接口
     * @param $uri
     * @param $data
     * @param $key
     * @param array $cert
     * @return mixed
     * @throws \Exception
     */
    public static function requestApi($uri, $data, $key, $cert = [])
    {
        $data['sign'] = Help::makeSign($data, $key);
        $result = Help::xmlToArray(self::getInstance()->post($uri, Help::arrayToXml($data)));
        if (!isset($result['return_code']) || $result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            throw new \Exception(sprintf("Wechat API Error '%s'.", $result['return_msg'] . (isset($result['err_code_des']) ?: '')));
        }
        return $result;
    }
}
