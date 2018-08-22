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
        $this->config['nonce_str'] = self::getNonceStr();
        $this->config['sign'] = self::MakeSign($this->config, $this->config['key']);
    }


    /**
     * 将array转换为xml格式数据
     * @param $array
     * @return string
     * @throws \Exception
     */
    public static function arrayToXml($array)
    {
        if (!is_array($array) || count($array) <= 0) {
            throw new \Exception('Invalid Array');
        }
        $xml = "<xml>";
        foreach ($array as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param $xml
     * @return mixed
     * @throws \Exception
     */
    public static function xmlToArray($xml)
    {
        if (!$xml) {
            throw new \Exception('Invalid xml');
        }
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }

    /**
     * 生成签名
     * @param $data
     * @param string $key api密钥
     * @return string
     */
    public static function MakeSign($data, $key)
    {
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string = self::ToUrlParams($data);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $sign = strtoupper($string);
        return $sign;
    }


    /**
     * 将参数拼接为url: key=value&key=value
     * @param $params
     * @return string
     */
    public static function ToUrlParams($params)
    {
        $buff = "";
        foreach ($params as $k => $v) {
            if ($k != "key" && $k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 获取支付结果通知数据
     * @return array|bool
     * @throws \Exception
     */
    public static function getNotifyData()
    {
        $xml = file_get_contents("php://input");
        if (empty($xml)) {
            return false;
        }
        $data = self::xmlToArray($xml);
        if (!empty($data['return_code'])) {
            if ($data['return_code'] == 'FAIL') {
                return false;
            }
        }
        return $data ?: [];
    }

    /**
     * 接收通知成功后应答输出XML数据
     * @throws \Exception
     */
    public static function replyNotify()
    {
        $data['return_code'] = 'SUCCESS';
        $data['return_msg'] = 'OK';
        $xml = self::arrayToXml($data);
        echo $xml;
    }

    /**
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return string
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            yield $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}
