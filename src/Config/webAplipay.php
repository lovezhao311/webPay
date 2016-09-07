<?php

return [
    // 是否剔除空值
    'reject_null_value' => true,
    // 返回方法 0 数组(默认) 1 url 2 直接跳转
    'return_type'       => '1',
    'secret_key'        => '',

    /**
     * 业务参数
     */
    'params'            => [
        'service'              => 'create_direct_pay_by_user',
        'partner'              => '2088702119574622',
        '_input_charset'       => 'UTF-8',
        'sign_type'            => 'MD5',

        'seller_id'            => '2088702119574622',
        // 其他设置
        'payment_type'         => 1,
        'extra_common_param'   => 'webalipay',
        // 是否需要买家实名认证
        'need_buyer_realnamed' => '',
        // 扫码支付方式
        'qr_pay_mode'          => '',
        // 商户自定二维码宽度
        'qrcode_width'         => '',
        // 花呗分期参数
        'hb_fq_param'          => '',
        // 超时设置
        'it_b_pay'             => '1c',
        // 默认支付方式
        'paymethod'            => 'directPay',
    ],
];
