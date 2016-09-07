<?php
namespace LuffyZhao\Library;

use LuffyZhao\Exception\PayException;

/**
 *
 */
abstract class AlipayPayment extends Payment
{
    protected $verifyNotifyGateway = "https://mapi.alipay.com/gateway.do?";
    /**
     * 处理支付数据
     * @return [type] [description]
     */
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

        // 剔除不是支付宝即时到账页面跳转同步通知参数
        $params = [];
        foreach ($this->returnKey as $key) {
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
        if (!$this->signVeryfy($params)) {
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
        // 剔除不是支付宝即时到账页面跳转异步通知参数
        $params = [];
        foreach ($this->notifyKey as $key) {
            if (isset($_GET[$key]) && (strlen($_GET[$key]) > 0)) {
                $params[$key] = $_GET[$key];
            }
        }
        // 签名验证
        if (!$this->signVeryfy($params)) {
            throw new PayException("该请求不是支付宝即时到账页面跳转异步通知的请求，原因：签名不正确！");
        }

        if ($this->config['verify_notify_id']) {
            $responseTxt = $this->verifyNotify($params['notify_id']);
            if (!preg_match("/true$/i", $responseTxt)) {
                throw new Exception("该请求不是支付宝即时到账页面跳转异步通知的请求，原因：notify_id 无效！");
            }
        }

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
     * 处理配置参数
     * @return [type] [description]
     */
    protected function _handleConfig()
    {
        return $this->config['params'];
    }

    /**
     * 验证NotifyId合法性
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function verifyNotify($notifyId)
    {
        if (!isset($this->config['cacert'])) {
            throw new PayException("ca证书路径地址不存在!");
        }

        $params = [
            'service'   => 'notify_verify',
            'partner'   => $this->config['params']['partner'],
            'notify_id' => $notifyId,
        ];

        $params    = array_map('urlencode', $params);
        $url       = $this->verifyNotifyGateway . http_build_query($params);
        $cacertUrl = $this->config['cacert'];
        return $this->_getHttpResponseGET($url, $cacertUrl);
    }

    /**
     * [setNotify description]
     * @param [type] $url [description]
     */
    public function setNotify($url)
    {
        if (!isset($this->config['params'])) {
            throw new PayException("请先设置配置参数!");
        }
        $this->config['params']['notify_url'] = $url;
    }
    /**
     * [setReturn description]
     * @param [type] $url [description]
     */
    public function setReturn($url)
    {
        if (!isset($this->config['params'])) {
            throw new PayException("请先设置配置参数!");
        }
        $this->config['params']['return_url'] = $url;
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
            case 'RSA':
                if (!isset($this->config['cert_path'])) {
                    throw new PayException("私钥存放目录不存在");
                }

                $sign = $this->rsaSign($signString);
            default:
                # code...
                break;
        }

        return $sign;
    }

    /**
     * 验证签名
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function signVeryfy($params)
    {
        switch ($params['sign_type']) {
            case 'MD5':
                return $params['sign'] == $this->sign($params);
                break;
            case 'RSA':
                return $this->rsaVeryfy($params);
                break;
            default:
                # code...
                break;
        }
    }

    /**
     * 验证签名 (rsa)
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function rsaVerify($params)
    {
        $signString = $this->_linkParams($params);

        if (!isset($this->config['cert_path'])) {
            throw new PayException("公钥存放目录不存在");
        }

        $file = $this->config['cert_path'] . "rsa_public_key.pem";
        if (!file_exists($file)) {
            throw new PayException("公钥不存在！");
        }

        $publicKey = file_get_contents($file);

        $res = openssl_get_publickey($publicKey);
        if ($res) {
            $result = (bool) openssl_verify($signString, base64_decode($params['sign']), $res);
        } else {
            throw new PayException("您的支付宝公钥格式不正确!");
        }
        openssl_free_key($res);
        return $result;
    }

    /**
     * 签名 (rsa)
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function rsaSign($signString)
    {
        $file = $this->config['cert_path'] . "rsa_private_key.pem";
        if (!file_exists($file)) {
            throw new PayException("私钥不存在！");
        }

        $privateKey = file_get_contents($file);
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

    private function _getHttpResponseGET($url, $cacertUrl)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 显示输出结果
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); //SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); //严格认证
        curl_setopt($curl, CURLOPT_CAINFO, $cacertUrl); //证书地址
        $responseText = curl_exec($curl);
        curl_close($curl);

        return $responseText;
    }

}
