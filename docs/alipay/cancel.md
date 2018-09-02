> 订单撤销

```php
    // out_trade_no trade_no 二选一
    $cancelOrder=[
        'out_trade_no'=>''
    ];
    Payment::alipay($config)->cancel($cancelOrder);
```