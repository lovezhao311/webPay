<?php

return [
    // 是否剔除空值
    'reject_null_value' => true,
    // 返回方法 0 数组(默认) 1 url 2 直接跳转
    'return_type'       => '1',

    'cert_path'         => __DIR__ . '/../../cert/wapalipay/',

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
