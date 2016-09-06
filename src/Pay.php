<?php
namespace LuffyZhao;

use LuffyZhao\Exception\PayException;

class Pay
{
    // 支付实例
    protected static $instance;
    // 支付方式实例
    protected $pay;

    public function __construct($type)
    {
        $this->payment($type);
    }

    /**
     * 初始化支付方式
     * @param  string $type 支付方式
     * @return object       支付方式实例
     */
    public static function instance($type)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($type);
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

}
