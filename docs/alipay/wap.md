> 手机网站支付

```php
    $wapOrder=[
        'subject'=>'',
        'out_trade_no'=>'',
        'total_amount'=>''
    ];
    Payment::alipay($config)->wap($wapOrder);
```

> 页面提交表单即可

```javascript
    document.forms['alipaysubmit'].submit();
```