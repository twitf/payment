> 支付宝

``` php
    use twitf\Payment\Payment;
    
    $config=[
        //应用ID,您的APPID。
        'app_id' => "",
        //应用私钥
        'application_private_key' => "",
        //异步通知地址
        'notify_url' => "",
        //同步跳转
        'return_url' => "",
        //编码格式
        'charset' => "UTF-8",
        //签名方式
        'sign_type'=>"RSA2",
        //支付宝公钥
        'alipay_public_key' => "",
        //沙箱模式
        //'mod'=>'sandbox'
    ];
```

> notify_url 回调验签

```php
    try{
        if (Payment::alipay($config)->verify($_POST)!==false){
            //执行你的操作
        }
    }catch (\Exception $e){
        var_dump($e->getMessage());
    }
```

> return_url 回调验签

```php
    try{
        if (Payment::alipay($config)->verify($_GET)!==false){
            //执行你的操作
        }
    }catch (\Exception $e){
        var_dump($e->getMessage());
    }
```