<?php
namespace LuffyZhao\Driver;

use LuffyZhao\Library\AlipayPayment;

class WapAlipay extends AlipayPayment
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

    protected $returnKey = ["is_success", "sign_type", "sign", "service", "notify_id", "notify_time", "notify_type", "out_trade_no", "trade_no", "subject", "payment_type", "trade_status", "seller_id", "total_fee", "body"];

    protected $notifyKey = ["notify_time", "notify_type", "notify_id", "sign_type", "sign", "out_trade_no", "subject", "payment_type", "trade_no", "trade_status", "gmt_create", "gmt_payment", "gmt_close", "seller_email", "buyer_email", "seller_id", "buyer_id", "price", "total_fee", "quantity", "body", "discount", "is_total_fee_adjust", "use_coupon", "refund_status", "gmt_refund"];

    public function _handleOrder()
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
