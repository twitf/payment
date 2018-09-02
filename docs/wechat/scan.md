> 扫码支付

```php
    $scanOrder = [
        'out_trade_no' => '',
        'body' => '',
        'total_fee' => '',
        'openid' => '',
        'notify_url' => '',
        'product_id' => ''
    ];

    Payment::wechat($config)->scan($scanOrder);
```