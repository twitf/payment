<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/30
 * Time: 10:25
 */

namespace twitf\Payment\Alipay;

trait Help
{
    /**
     * @param $data 待签名字符串
     * @param $privateKey 商户私钥
     * @param $signType 签名方式
     * @return string
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
        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');
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
     * @param $params
     * @param $charset
     * @return string
     */
    public static function getSignContent($params,$charset)
    {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === self::checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = self::characet($v, $charset);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
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
     * @param $targetCharset 要转换的编码
     * @return string
     */
    public static function characet($data, $targetCharset)
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
     * @param $params 请求参数数组
     * @return 提交表单HTML文本
     */
    public static function buildRequestForm($url,$params,$charset) {

        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$url."?charset=".trim($charset)."' method='POST'>";
        while (list ($key, $val) = each ($params)) {
            if (false === self::checkEmpty($val)) {
                $val = str_replace("'","&apos;",$val);
                $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
            }
        }
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='ok' style='display:none;''></form>";
        return $sHtml;
    }
}
