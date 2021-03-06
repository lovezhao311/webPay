<?php
namespace LuffyZhao\Driver;

use LuffyZhao\Library\AlipayPayment;

class WebAlipay extends AlipayPayment
{
    protected $gateway = "https://mapi.alipay.com/gateway.do?";

    // 支付方式所需字段与订单字段对照
    protected $requireKey = [
        'service'        => 'require',
        'partner'        => 'require|preg:/^2088\d{12}$/',
        '_input_charset' => 'require|in:UTF-8,GBK,GB2312',
        'sign_type'      => 'require|in:DSA,RSA,MD5',
        'notify_url'     => 'require|url',
        'return_url'     => 'require|url',
        'seller_id'      => 'require|equal:@partner',
        'payment_type'   => 'equal:1',
        'out_trade_no'   => 'require|length:0,64',
        'subject'        => 'require|length:0,255|specialString',
        'total_fee'      => 'require|range:0.01,100000000',
    ];
    // 页面跳转同步参数
    protected $returnKey = ['is_success', 'sign_type', 'sign', 'out_trade_no', 'subject', 'payment_type', 'exterface', 'trade_no', 'trade_status', 'notify_id', 'notify_time', 'notify_type', 'seller_email', 'buyer_email', 'seller_id', 'buyer_id', 'total_fee', 'body', 'extra_common_param'];
    // 服务器异步通知参数
    protected $notifyKey = ['notify_time', 'notify_type', 'notify_id', 'sign_type', 'sign', 'out_trade_no', 'subject', 'payment_type', 'trade_no', 'trade_status', 'gmt_create', 'gmt_payment', 'gmt_close', 'refund_status', 'gmt_refund', 'seller_email', 'buyer_email', 'seller_id', 'buyer_id', 'price', 'total_fee', 'quantity', 'body', 'discount', 'is_total_fee_adjust', 'use_coupon', 'extra_common_param', 'business_scene'];

    /**
     * 处理订单参数
     * @return [type] [description]
     */
    protected function _handleOrder()
    {
        return [
            'out_trade_no' => $this->order['out_trade_no'],
            'subject'      => $this->order['subject'],
            'total_fee'    => $this->order['total_fee'],
            'body'         => $this->order['body'],
            'show_url'     => $this->order['show_url'],
            'goods_type'   => $this->order['goods_type'],
        ];

    }

}

/**
out_trade_no
subject
total_fee
body 商品描述
show_url 商品展示网址
goods_type
 */
