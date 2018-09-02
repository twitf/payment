> 转账接口

```php
    $transferOrder=[
        'out_biz_no'=>'',
        'payee_type'=>'',
        'payee_account'=>'',
        'amount'=>''
    ];
    Payment::alipay($config)->transfer($transferOrder);
```