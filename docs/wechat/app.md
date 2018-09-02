> App支付

```php
    $appOrder = [
        'out_trade_no' =>'',
        'body' => '',
        'total_fee' => '',
        'notify_url' => ''
    ];

    Payment::wechat($config)->app($appOrder);
```