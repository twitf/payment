> 电脑网站支付

```php
    $pcOrder=[
        'subject'=>'',
        'out_trade_no'=>'',
        'total_amount'=>''
    ];
    Payment::alipay($config)->pc($pcOrder);
```

> 页面提交表单即可

```javascript
    document.forms['alipaysubmit'].submit();
```
