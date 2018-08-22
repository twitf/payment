<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/22
 * Time: 13:43
 */
$config=[
    'appid'=>1,
    'mch'=>2,
    'attch'=>[
        'a'=>3,
        'b'=>4
    ]
];

$a=\Payment\Payment::wechat($config)->mp();
