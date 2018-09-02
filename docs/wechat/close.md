> 关闭订单

```php
$closeOrder=[
    'out_trade_no'=>''
];
Payment::wechat($Config)->close($closeOrder);
```
