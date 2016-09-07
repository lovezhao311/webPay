<?php
namespace LuffyZhao\Driver;

use LuffyZhao\Exception\PayException;
use LuffyZhao\Library\Payment;

class MobileWebAlipay extends Payment
{
    // protected $gateway = "https://mapi.alipay.com/gateway.do?";
    protected $gateway = "https://openapi.alipaydev.com/gateway.do?";

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
     * 验证返回
     * @return [type] [description]
     */
    public function returnVerify()
    {
        if (!isset($_GET['is_success'])) {
            throw new PayException("该请求不是支付宝即时到账页面跳转同步通知的请求！");
        }

        $paramsKey = ['is_success', 'sign_type', 'sign', 'out_trade_no', 'subject', 'payment_type', 'exterface', 'trade_no', 'trade_status', 'notify_id', 'notify_time', 'notify_type', 'seller_email', 'buyer_email', 'seller_id', 'buyer_id', 'total_fee', 'body', 'extra_common_param'];
        // 剔除不是支付宝即时到账页面跳转同步通知参数
        $params = [];
        foreach ($paramsKey as $key) {
            if (isset($_GET[$key]) && (strlen($_GET[$key]) > 0)) {
                $params[$key] = $_GET[$key];
            }
        }

        if ($params['is_success'] != 'T') {
            return [
                'status'     => 0,
                'order_code' => $params['out_trade_no'],
                'pay_code'   => $params['trade_no'],
            ];
        }

        // 签名验证
        $sign = $this->sign($params);
        if ($sign != $params['sign']) {
            throw new PayException("该请求不是支付宝即时到账页面跳转同步通知的请求，原因：签名不正确！");
        }

        return [
            'status'     => 1,
            'order_code' => $params['out_trade_no'],
            'pay_code'   => $params['trade_no'],
        ];
    }

    /**
     * 验证通知
     * @return bool array 完成付款 false 未完成付款
     */
    public function notifyVerify()
    {
        $paramsKey = ['notify_time', 'notify_type', 'notify_id', 'sign_type', 'sign', 'out_trade_no', 'subject', 'payment_type', 'trade_no', 'trade_status', 'gmt_create', 'gmt_payment', 'gmt_close', 'refund_status', 'gmt_refund', 'seller_email', 'buyer_email', 'seller_id', 'buyer_id', 'price', 'total_fee', 'quantity', 'body', 'discount', 'is_total_fee_adjust', 'use_coupon', 'extra_common_param', 'business_scene'];

        // 剔除不是支付宝即时到账页面跳转异步通知参数
        $params = [];
        foreach ($paramsKey as $key) {
            if (isset($_GET[$key]) && (strlen($_GET[$key]) > 0)) {
                $params[$key] = $_GET[$key];
            }
        }
        // 签名验证
        $sign = $this->sign($params);
        if ($sign != $params['sign']) {
            throw new PayException("该请求不是支付宝即时到账页面跳转异步通知的请求，原因：签名不正确！");
        }

        echo 'success';

        switch ($params['refund_status']) {
            case 'TRADE_SUCCESS':
            case 'TRADE_PENDING':
            case 'TRADE_FINISHED':
                return [
                    'status'     => 1,
                    'order_code' => $params['out_trade_no'],
                    'pay_code'   => $params['trade_no'],
                ];
                break;
            default:
                return [
                    'status'     => 0,
                    'order_code' => $params['out_trade_no'],
                    'pay_code'   => $params['trade_no'],
                ];
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
        $sign       = '';
        switch ($params['sign_type']) {
            case 'MD5':
                $sign = md5($signString . $this->config['secret_key']);
                break;
            case 'RSA':
                $sign = $this->rsa($signString);
            default:
                # code...
                break;
        }

        return $sign;
    }

    /**
     * rsa签名方式
     * @param  [type] $signString [description]
     * @return [type]             [description]
     */
    public function rsa($signString)
    {
        $privateKey = file_get_contents(__DIR__ . "/../../rsa/alipay/rsa_private_key.pem");
        $res        = openssl_get_privatekey($privateKey);
        if ($res) {
            openssl_sign($signString, $sign, $res);
        } else {
            throw new PayException("私钥格式不正确");

        }
        openssl_free_key($res);
        //base64编码
        $sign = base64_encode($sign);
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
        reset($params);

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
        return $this->config['params'];
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
        if (!isset($this->config['params'])) {
            throw new PayException("请先设置配置参数!");
        }
        $this->config['params']['notify_url'] = $url;
    }

    public function setReturn($url)
    {
        if (!isset($this->config['params'])) {
            throw new PayException("请先设置配置参数!");
        }
        $this->config['params']['return_url'] = $url;
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