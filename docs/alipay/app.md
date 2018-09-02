> App支付

```php
    $appOrder=[
        'subject'=>'',
        'out_trade_no'=>'',
        'total_amount'=>''
    ];
    Payment::alipay($config)->app($appOrder);
```