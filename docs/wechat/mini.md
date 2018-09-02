> 小程序支付

```php
    $miniOrder = [
        'out_trade_no' => '',
        'body' => '',
        'total_fee' => '',
        'openid' => '',
        'notify_url' => ''
    ];

    Payment::wechat($config)->mini($miniOrder);
```