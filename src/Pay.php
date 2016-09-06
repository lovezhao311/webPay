<?php
namespace LuffyZhao;

use LuffyZhao\Exception\PayException;

class Pay
{
    // 支付实例
    protected static $instance;
    // 支付方式实例
    protected $pay;

    public function __construct($type, $config)
    {
        $this->payment($type);
        if (!empty($config) && is_array($config)) {
            $this->setConfig($config);
        }
    }

    /**
     * 初始化支付方式
     * @param  string $type 支付方式
     * @return object       支付方式实例
     */
    public static function instance($type, $config = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($type, $config);
        }
        return self::$instance;
    }

    /**
     * 设置当前支付方式
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public function payment($type)
    {
        $class = false !== strpos($type, '\\') ? $type : '\\LuffyZhao\\Driver\\' . ucfirst($type);
        if (!class_exists($class)) {
            throw new PayException("支付方式不存在！");
        }
        $this->pay = new $class();
        return $this;
    }

    /**
     * 设置 config
     * @param array $config [description]
     */
    public function __call($method, $params)
    {
        if (!method_exists($this->pay, $method)) {
            $class = get_class($this->pay);
            throw new PayException("{$class}的 {$method} 方法不存在！");
        }
        if (substr($method, 0, 3) == 'set') {
            call_user_func_array([$this->pay, $method], $params);
        } else {
            return call_user_func_array([$this->pay, $method], $params);
        }
        return $this;
    }

}
