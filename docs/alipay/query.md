> 订单查询

```php
   
    $queryOrder=[
         // out_trade_no trade_no 二选一
        'out_trade_no'=>''
    ];
    //查询订单
    Payment::alipay($config)->query($queryOrder);
```

> 退款订单查询

```php
    
    $queryOrder=[
        // out_trade_no trade_no 二选一
        'out_trade_no'=>'',
        'out_request_no'=>''
    ];
    //查询退款订单
    Payment::alipay($config)->query($queryOrder,true);
```