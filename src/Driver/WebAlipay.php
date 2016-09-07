<?php
namespace LuffyZhao\Driver;

use LuffyZhao\Library\Payment;

class WebAlipay extends Payment
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
        'subject'        => 'require|length:0,255',
        'total_fee'      => 'require|range:0.01,100000000',
    ];

    public function handle()
    {
        $params = $this->_handleParams();

        switch ($this->config['return_type']) {
            case 0:
                return [
                    'gateway' => $this->gateway,
                    'params'  => $params,
                ];

                break;
            case 1:
                $params = array_map('urlencode', $params);
                return $this->gateway . http_build_query($params);
                break;

            case 2:
            default:
                $params = array_map('urlencode', $params);
                $url    = $this->gateway . http_build_query($params);

                header("Location:{$url}");
                break;
        }
    }

    /**
     * 生成签名字符串
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function sign($params)
    {
        $signString = $this->_linkParams($params);

        $sign = '';
        switch ($params['sign_type']) {
            case 'MD5':
                $sign = md5($signString . $this->config['secret_key']);
                break;
            default:
                # code...
                break;
        }

        return $sign;
    }

    /**
     * 拼接参数
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private function _linkParams($params)
    {
        // 排序
        ksort($params);

        $link = '';
        foreach ($params as $key => $value) {
            if ($key == 'sign' || $key == 'sign_type') {
                continue;
            }
            $link .= "{$key}={$value}&";
        }

        $link = rtrim($link, '&');

        return $link;
    }

    /**
     * 处理配置参数
     * @return [type] [description]
     */
    protected function _handleConfig()
    {
        return [
            'service'              => $this->config['service'],
            'partner'              => $this->config['partner'],
            '_input_charset'       => $this->config['_input_charset'],
            'sign_type'            => $this->config['sign_type'],
            'notify_url'           => $this->config['notify_url'],
            'return_url'           => $this->config['return_url'],
            'seller_id'            => $this->config['seller_id'],
            'payment_type'         => $this->config['payment_type'],
            'extra_common_param'   => $this->config['extra_common_param'],
            'need_buyer_realnamed' => $this->config['need_buyer_realnamed'],
            'qr_pay_mode'          => $this->config['qr_pay_mode'],
            'qrcode_width'         => $this->config['qrcode_width'],
            'hb_fq_param'          => $this->config['hb_fq_param'],
            'it_b_pay'             => $this->config['it_b_pay'],
            'paymethod'            => $this->config['paymethod'],
        ];
    }

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

    public function setNotify($url)
    {
        $this->config['notify_url'] = $url;
    }

    public function setReturn($url)
    {
        $this->config['return_url'] = $url;
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
