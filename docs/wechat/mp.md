> 公众号支付

```php
    $mpOrder = [
        'out_trade_no' =>'',
        'body' => '',
        'total_fee' => '',
        'openid' => '',
        'notify_url' => ''
    ];

    Payment::wechat($config)->mp($mpOrder);
```