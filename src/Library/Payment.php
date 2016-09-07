<?php
namespace LuffyZhao\Library;

use LuffyZhao\Exception\PayException;

/**
 * 文档地址
 * https://doc.open.alipay.com/docs/doc.htm?spm=a219a.7629140.0.0.hL7ca4&treeId=60&articleId=104741&docType=1
 * https://doc.open.alipay.com/docs/doc.htm?spm=a219a.7629140.0.0.kNStrq&treeId=62&articleId=104741&docType=1
 */
abstract class Payment
{
    // 支付方式配置参数
    protected $config = [];
    // 订单数据
    protected $order = [];
    // 支付方式所需参数
    protected $data = [];

    /**
     * 处理支付数据
     * @return [type] [description]
     */
    abstract public function handle();

    /**
     * 验证返回是否正确
     * @return [type] [description]
     */
    abstract public function returnVerify();

    /**
     * 验证通知是否正确
     * @return [type] [description]
     */
    abstract public function notifyVerify();

    /**
     * 签名
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    abstract protected function sign($params);

    /**
     * 通知地址
     * 因为不同的支付方式可能参数名不一样
     * 所以请在子类中重写这个方法
     * @param [type] $url [description]
     */
    abstract public function setNotify($url);

    /**
     * 返回地址
     * 因为不同的支付方式可能参数名不一样
     * 所以请在子类中重写这个方法
     * @param [type] $url [description]
     */
    abstract public function setReturn($url);

    /**
     * 构造函数
     * @param [type] $config [description]
     */
    public function __construct()
    {

    }

    /**
     * 设置配置文件
     * @param [type] $config [description]
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function setOrder(array $params)
    {
        $this->order = $params;
    }

    /**
     * 验证参数
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    protected function check(array $params)
    {
        if (!isset($this->requireKey)) {
            return true;
        }

        foreach ($this->requireKey as $key => $rules) {
            if (strpos($rules, '|') !== false) {
                $rules = explode('|', $rules);
            } else {
                $rules = (array) $rules;
            }

            foreach ($rules as $rule) {

                $verify = '';
                if (strpos($rule, ':') !== false) {
                    list($rule, $verify) = explode(':', $rule, 2);
                }

                if ($rule == 'require') {
                    if (!isset($params[$key])) {
                        throw new PayException("字段 {$key} 必须填写且不能为空！");
                    }
                    continue;
                } elseif (!isset($params[$key])) {
                    continue;
                }

                if (!method_exists("\\LuffyZhao\\Library\\Verify", $rule)) {
                    throw new PayException("字段 {$key} {$rule} 验证方法不存在！");
                }

                $void = call_user_func_array(["\\LuffyZhao\\Library\\Verify", $rule], [
                    $params[$key],
                    $verify,
                    $params,
                ]);

                if (!$void) {
                    throw new PayException("字段 {$key} 验证 {$rule} 不通过！");
                }

            }
        }
    }

    /**
     * 整理所需参数
     * @return [type] [description]
     */
    protected function _handleParams()
    {
        $commonParams   = $this->_handleConfig();
        $businessParams = $this->_handleOrder();

        $params = array_merge($commonParams, $businessParams);

        if ($this->config['reject_null_value']) {
            foreach ($params as $key => $value) {
                if (strlen($value) == 0) {
                    unset($params[$key]);
                }
            }
        }

        $this->check($params);

        $params['sign'] = $this->sign($params);

        return $params;
    }

}
