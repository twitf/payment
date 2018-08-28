<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/22
 * Time: 15:51
 */

namespace twitf\Payment\Wechat;

trait Help
{
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
    public static function makeSign($data, $key)
    {
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string = self::toUrlParams($data);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $sign = strtoupper($string);
        return $sign;
    }

    public static function makeMiniSign()
    {

    }

    /**
     * 将参数拼接为url: key=value&key=value
     * @param $params
     * @return string
     */
    public static function toUrlParams($params)
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
     * @throws \Exception
     */
    public static function getNonceStr($length = 32)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = static::randomBytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * 生成随机的字节
     * @param int $length
     * @return string
     * @throws \Exception
     */
    public static function randomBytes($length = 16)
    {
        if (function_exists('random_bytes')) {
            $bytes = random_bytes($length);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length, $strong);
            if (false === $bytes || false === $strong) {
                throw new \Exception('Unable to generate random string.');
            }
        } else {
            throw new \Exception('OpenSSL extension is required for PHP 5 users.');
        }

        return $bytes;
    }

    public static function getClientIp()
    {
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
        }
        return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
    }


    /**
     * 获取jsapi支付的参数
     * @param $UnifiedOrderResult
     * @param $key
     * @return false|string
     * @throws \Exception
     */
    public static function getJsApiParameters($UnifiedOrderResult, $key)
    {
        $data = [];
        $data['appId'] = $UnifiedOrderResult['appid'];
        $data['timeStamp'] = (string)time();
        $data['nonceStr'] = Help::getNonceStr();
        $data['package'] = "prepay_id=" . $UnifiedOrderResult['prepay_id'];
        $data['signType'] = 'MD5';
        $data['paySign'] = self::MakeSign($data, $key);
        return $data;
    }

    /**
     * 获取当前链接地址
     * @return string
     */
    public static function getCurrentUrl()
    {
        $protocol = 'http://';
        if ((!empty($_SERVER['HTTPS']) && 'off' !== $_SERVER['HTTPS']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ?: 'http') === 'https') {
            $protocol = 'https://';
        }
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * 获取当前域名链接（不包含参数）
     * @return string
     */
    public static function getHostInfo()
    {
        $http = 'http';
        $secure = false;
        if (isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0) {
            $http = 'https';
            $secure = true;
        }
        if (isset($_SERVER['HTTP_HOST'])) {
            $hostInfo = $http . '://' . $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            if ($secure) {
                $port = isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 443;
            } else {
                $port = isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 80;
            }
            $hostInfo = $http . '://' . $_SERVER['SERVER_NAME'];
            if (($port !== 80 && !$secure) || ($port !== 443 && $secure)) {
                $hostInfo .= ':' . $port;
            }
        }
        return $hostInfo;
    }
}
