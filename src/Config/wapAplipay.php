<?php

return [
    // 是否剔除空值
    'reject_null_value' => true,
    // 返回方法 0 数组(默认) 1 url 2 直接跳转
    'return_type'       => '1',
    // md5 签名所用的key
    'secret_key'        => '',
    // rsa 签名 公钥/密钥存放目录
    'cert_path'         => __DIR__ . '/../../cert/wapalipay/',
    //ca证书路径地址，用于curl中ssl校验
    //请保证cacert.pem文件在当前文件夹目录中
    'cacert'            => getcwd() . '\\cacert.pem',
    // 是否严格验证 notify_id
    'verify_notify_id'  => false,

    'params'            => [
        "service"        => "alipay.wap.create.direct.pay.by.user",
        "partner"        => "2088102168675921",
        "_input_charset" => "UTF-8",
        "sign_type"      => "RSA",
        "seller_id"      => "2088102168675921",
        "payment_type"   => 1,
        // 商户与支付宝约定的营销参数，为Key:Value键值对，如需使用，请联系支付宝技术人员。
        "promo_params"   => "",
        // 花呗分期参数
        "hb_fq_param"    => "",
        // 超时时间
        "it_b_pay"       => "",
        // 是否支持APP
        "app_pay"        => "Y",
    ],
];
