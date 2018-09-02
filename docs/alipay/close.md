> 订单关闭

```php
    // out_trade_no trade_no 二选一
    $closeOrder=[
        'out_trade_no'=>''
    ];
    Payment::alipay($config)->close($closeOrder);
```