> 查询订单

```php
$queryOrder=[
    //transaction_id out_trade_no 二选一
    'out_trade_no'=>''
];
Payment::wechat($Config)->query($queryOrder);
```

> 查询退款订单

```php
$queryOrder=[
    //transaction_id out_trade_no out_refund_no refund_id 四选一
    'out_trade_no'=>''
];
Payment::wechat($Config)->query($queryOrder,true);
```
