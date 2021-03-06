<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/30
 * Time: 10:25
 */

namespace twitf\Payment\Alipay;

class Help
{
    use \twitf\Payment\HttpRequest;

    private static $instance;

    protected $baseUri = 'https://openapi.alipay.com/gateway.do';

    //表示等待服务器响应超时的最大值，使用 0 将无限等待 (默认行为).
    protected $connect_timeout = 5;

    //请求超时的秒数。使用 0 无限期的等待(默认行为)。
    protected $timeout = 5;

    public static function setBaseUrl($isSandbox)
    {
        if ($isSandbox) {
            self::getInstance()->baseUri = 'https://openapi.alipaydev.com/gateway.do';
        }
        return self::getInstance()->baseUri;
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
     * @param $publicKey
     * @return mixed
     * @throws \Exception
     */
    public static function requestApi($uri, $data, $publicKey)
    {
        $response = self::getInstance()->post('', $data);
        $response = mb_convert_encoding($response, 'UTF-8', 'GB2312');
        $result = json_decode($response, true);
        $responseName = str_replace('.', '_', $data['method']) . '_response';
        if (!isset($result['sign']) || !isset($result[$responseName]['code']) || $result[$responseName]['code'] != '10000') {
            throw new \Exception(sprintf("Alipay API Error: '%s'.", $result[$responseName]['msg'] . (isset($result[$responseName]['sub_code']) ? $result[$responseName]['sub_code'] : '')));
        }
        if (self::verifySign(json_encode($result[$responseName], JSON_UNESCAPED_UNICODE), $result['sign'], $publicKey, $data['sign_type'])) {
            return $result;
        }
        throw new \Exception("Sign error");
    }


    /**
     * @param $data 待签名字符串
     * @param $privateKey 应用私钥
     * @param $signType 签名方式
     * @return string
     * @throws \Exception
     */
    public static function makeSign($data, $privateKey, $signType)
    {
        if (self::getExtension($privateKey) == 'pem') {
            $res = openssl_get_privatekey($privateKey);
        } else {
            $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
                wordwrap($privateKey, 64, "\n", true) .
                "\n-----END RSA PRIVATE KEY-----";
        }
        if (!$res) {
            throw new \Exception(sprintf("您使用的私钥格式错误，请检查商户'%s'私钥配置.", $signType));
        }
        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $res);
        }
        //释放资源
        if (self::getExtension($privateKey) == 'pem') {
            openssl_free_key($res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }


    /**
     * @param array $params 待组合参数数组
     * @param string $charset 请求编码
     * @param bool $verify 验证签名时 传true
     * @return string
     */
    public static function getSignContent($params, $charset, $verify = false)
    {
        ksort($params);
        $stringToBeSigned = "";
        foreach ($params as $k => $v) {
            if (!$verify && false === self::checkEmpty($v) && "@" != substr($v, 0, 1) && $k != 'sign') {
                $stringToBeSigned .= $k . '=' . $v . '&';
            }
            if ($verify && $k != 'sign' && $k != 'sign_type') {
                $stringToBeSigned .= $k . '=' . $v . '&';
            }
        }
        return self::charset(trim($stringToBeSigned, '&'), $charset);
    }

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    public static function checkEmpty($value)
    {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param string $targetCharset 要转换的编码
     * @return string
     */
    public static function charset($data, $targetCharset)
    {
        if (!empty($data)) {
            if (strcasecmp('UTF-8', $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, 'UTF-8');
            }
        }
        return $data;
    }

    public static function getExtension($name)
    {
        return strtolower(pathinfo($name, PATHINFO_EXTENSION));
    }


    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $url
     * @param array $params 请求参数数组
     * @param string $charset 字符编码
     * @return string 提交表单HTML文本
     */
    public static function buildRequestForm($url, $params, $charset)
    {

        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='" . $url . "?charset=" . trim($charset) . "' method='POST'>";
        foreach ($params as $key => $val){
            if (false === self::checkEmpty($val)) {
                $val = str_replace("'", "&apos;", $val);
                $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
            }
        }
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml . "<input type='submit' value='ok' style='display:none;''></form>";
        //页面上触发 document.forms['alipaysubmit'].submit(); 即可
        return $sHtml;
    }


    /**
     * @param string $data 待验证的数组
     * @param string $sign 签名
     * @param $PublicKey 支付宝公钥
     * @param string $signType 签名类型
     * @return bool
     * @throws \Exception
     */
    public static function verifySign($data, $sign, $PublicKey, $signType)
    {
        if (self::getExtension($PublicKey) == 'pem') {
            $res = openssl_get_publickey($PublicKey);
        } else {
            $res = "-----BEGIN PUBLIC KEY-----\n" .
                wordwrap($PublicKey, 64, "\n", true) .
                "\n-----END PUBLIC KEY-----";
        }
        if (!$res) {
            throw new \Exception(sprintf("支付宝'%s'公钥错误。请检查公钥文件格式是否正确.", $signType));
        }
        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }
        if (self::getExtension($PublicKey) == 'pem') {
            //释放资源
            openssl_free_key($res);
        }
        return $result;
    }
}
