> 微信支付

```php
    use twitf\Payment\Payment;
    
    $config = [
        'key' => '',
        'appid' => '',
        'mch_id' => ''
    ];
```

> 回调验签

```php
    try{
        $notify=Payment::wechat($config)->verify();
    }catch (\Exception $e){
        var_dump($e->getMessage());    
    }
```
